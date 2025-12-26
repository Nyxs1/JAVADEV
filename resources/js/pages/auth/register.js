/**
 * Register Page Manager
 * Handles OTP flow, validation with touched state, password toggle, and form submission
 * Gen Z copywriting style: "masih" / "belum" - friendly, not blaming
 */

// Configuration
const OTP_CONFIG = {
    RESEND_COOLDOWN_SECONDS: 30,
    OTP_EXPIRY_MINUTES: 30,
    OTP_MAX_ATTEMPTS: 3,
};

// Validation Messages (English)
const REGISTER_MESSAGES = {
    username: "Username is required.",
    email: "Email is required.",
    emailInvalid: "Please enter a valid email address.",
    emailRequired: "Email is required.",
    verification: "Verification code is required.",
    verificationLength: "Verification code must be 6 digits.",
    password: "Password is required.",
    passwordMin: "Password must be at least 8 characters.",
    passwordWeak: "Password must contain uppercase, lowercase, and number.",
    confirmPassword: "Please confirm your password.",
    confirmMismatch: "Passwords do not match.",
};

// Password strength regex: at least 1 lowercase, 1 uppercase, 1 digit, min 8 chars
const PASSWORD_REGEX = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

// Reusable validation helpers for register
const RegisterValidation = {
    markTouched(input) {
        if (input) input.dataset.touched = "1";
    },

    isTouched(input) {
        return input?.dataset.touched === "1";
    },

    showError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}Error`);
        const inputWrapper = document.getElementById(`${fieldName}Input`);

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }

        if (inputWrapper) {
            inputWrapper.classList.add("error");
        }
    },

    clearError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}Error`);
        const inputWrapper = document.getElementById(`${fieldName}Input`);

        if (errorElement) {
            errorElement.style.display = "none";
        }

        if (inputWrapper) {
            inputWrapper.classList.remove("error");
        }
    },

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    // Evaluate password strength: Weak / Medium / Strong
    evaluatePasswordStrength(password) {
        if (!password || password.length < 8) {
            return { level: "weak", label: "Weak", score: 0 };
        }

        let score = 0;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        if (password.length >= 12) score++;

        if (score <= 2) {
            return { level: "weak", label: "Weak", score: 1 };
        } else if (score <= 3) {
            return { level: "medium", label: "Medium", score: 2 };
        } else {
            return { level: "strong", label: "Strong", score: 3 };
        }
    },

    updatePasswordStrength(password) {
        const strengthContainer = document.getElementById("passwordStrength");
        if (!strengthContainer) return;

        if (!password || password.length === 0) {
            strengthContainer.style.display = "none";
            return;
        }

        const { level, label, score } = this.evaluatePasswordStrength(password);
        strengthContainer.style.display = "flex";
        strengthContainer.className = `password-strength password-strength--${level}`;

        const bar = strengthContainer.querySelector(".password-strength__bar");
        const text = strengthContainer.querySelector(
            ".password-strength__text"
        );

        if (bar) {
            bar.style.width = `${(score / 3) * 100}%`;
        }
        if (text) {
            text.textContent = label;
        }
    },

    bindRequiredField(input, fieldName, message, options = {}) {
        if (!input) return;

        input.addEventListener("focus", () => {
            this.markTouched(input);
        });

        input.addEventListener("blur", () => {
            if (!this.isTouched(input)) return;

            const value = input.value.trim();

            if (!value) {
                this.showError(fieldName, message);
                return;
            }

            if (options.isEmail && !this.isValidEmail(value)) {
                this.showError(fieldName, REGISTER_MESSAGES.emailInvalid);
                return;
            }

            if (options.minLength && value.length < options.minLength) {
                this.showError(fieldName, options.minLengthMsg || message);
                return;
            }

            if (options.regex && !options.regex.test(value)) {
                this.showError(fieldName, options.regexMsg || message);
                return;
            }

            if (options.exactLength && value.length !== options.exactLength) {
                this.showError(fieldName, options.exactLengthMsg || message);
                return;
            }

            this.clearError(fieldName);
        });

        input.addEventListener("input", () => {
            const value = input.value.trim();

            // Update password strength if this is password field
            if (fieldName === "password") {
                this.updatePasswordStrength(value);
            }

            if (!value) return;

            if (options.isEmail) {
                if (this.isValidEmail(value)) {
                    this.clearError(fieldName);
                }
                return;
            }

            if (options.minLength) {
                if (value.length >= options.minLength) {
                    // Also check regex if present
                    if (options.regex) {
                        if (options.regex.test(value)) {
                            this.clearError(fieldName);
                        }
                    } else {
                        this.clearError(fieldName);
                    }
                }
                return;
            }

            if (options.exactLength) {
                if (value.length === options.exactLength) {
                    this.clearError(fieldName);
                }
                return;
            }

            this.clearError(fieldName);
        });
    },
};

class RegisterManager {
    constructor() {
        this.form = document.getElementById("registerForm");
        if (!this.form) return;

        this.btnGetCode = document.getElementById("btnGetCode");
        this.btnGetCodeText = this.btnGetCode?.querySelector(
            ".btn-get-code__text"
        );
        this.verificationCode = document.getElementById("verificationCode");
        this.submitBtn = document.getElementById("submitBtn");
        this.devOtpPanel = document.getElementById("devOtpPanel");

        // Input elements
        this.usernameInput = this.form.querySelector('input[name="username"]');
        this.emailInput = this.form.querySelector('input[name="email"]');

        // Password toggle elements
        this.passwordInput = document.getElementById("register-password-input");
        this.passwordIcon = document.getElementById("register-eye-icon");
        this.passwordToggle = document.getElementById("password-toggle");

        this.confirmInput = document.getElementById("confirm-password-input");
        this.confirmIcon = document.getElementById("confirm-eye-icon");
        this.confirmToggle = document.getElementById("confirm-toggle");

        // Cooldown state (single source of truth)
        this.countdownTimer = null;
        this.countdownSeconds = 0;
        this.devOtpTimer = null;

        // OTP persistence for dev mode (re-show during cooldown)
        this.lastOtpCode = null;
        this.lastOtpEmail = null;
        this.lastOtpAt = 0;

        // Toast throttle (anti-spam)
        this.lastCooldownToastAt = 0;

        this.init();
    }

    init() {
        this.bindEvents();
        this.initPasswordToggles();
        this.initInputValueDetection();
        this.initRequiredValidation();
        this.initConfirmPasswordValidation();
        this.restoreCountdown();
    }

    bindEvents() {
        if (this.btnGetCode) {
            this.btnGetCode.addEventListener("click", () =>
                this.handleSendCode()
            );
        }

        this.form.addEventListener("submit", (e) => this.handleSubmit(e));
    }

    // Check if cooldown is active
    isCooldownActive() {
        return this.countdownSeconds > 0 && this.btnGetCode?.disabled;
    }

    initPasswordToggles() {
        if (this.passwordToggle && this.passwordInput && this.passwordIcon) {
            this.passwordToggle.addEventListener("click", (e) => {
                e.preventDefault();
                this.togglePassword(
                    this.passwordInput,
                    this.passwordIcon,
                    this.passwordToggle
                );
            });
        }

        if (this.confirmToggle && this.confirmInput && this.confirmIcon) {
            this.confirmToggle.addEventListener("click", (e) => {
                e.preventDefault();
                this.togglePassword(
                    this.confirmInput,
                    this.confirmIcon,
                    this.confirmToggle
                );
            });
        }
    }

    togglePassword(input, icon, button) {
        const isPassword = input.type === "password";

        input.type = isPassword ? "text" : "password";
        icon.src = isPassword
            ? "/assets/icons/eye-off-icon.svg"
            : "/assets/icons/eye-icon.svg";
        icon.alt = isPassword ? "Hide Password" : "Show Password";
        button.classList.toggle("active", isPassword);
    }

    initInputValueDetection() {
        const inputs = this.form.querySelectorAll(".register-input input");

        inputs.forEach((input) => {
            const updateState = () => {
                const wrapper = input.closest(".register-input");
                if (!wrapper) return;

                if (input.value.trim() !== "") {
                    wrapper.classList.add("has-value");
                } else {
                    wrapper.classList.remove("has-value");
                }
            };

            input.addEventListener("input", updateState);
            input.addEventListener("blur", updateState);
            updateState();
        });
    }

    initRequiredValidation() {
        RegisterValidation.bindRequiredField(
            this.usernameInput,
            "username",
            REGISTER_MESSAGES.username
        );

        RegisterValidation.bindRequiredField(
            this.emailInput,
            "email",
            REGISTER_MESSAGES.email,
            { isEmail: true }
        );

        RegisterValidation.bindRequiredField(
            this.passwordInput,
            "password",
            REGISTER_MESSAGES.password,
            {
                minLength: 8,
                minLengthMsg: REGISTER_MESSAGES.passwordMin,
                regex: PASSWORD_REGEX,
                regexMsg: REGISTER_MESSAGES.passwordWeak,
            }
        );

        RegisterValidation.bindRequiredField(
            this.confirmInput,
            "confirmPassword",
            REGISTER_MESSAGES.confirmPassword
        );

        RegisterValidation.bindRequiredField(
            this.verificationCode,
            "verification",
            REGISTER_MESSAGES.verification,
            {
                exactLength: 6,
                exactLengthMsg: REGISTER_MESSAGES.verificationLength,
            }
        );
    }

    initConfirmPasswordValidation() {
        if (!this.confirmInput || !this.passwordInput) return;

        // Real-time validation when typing in confirm password field
        this.confirmInput.addEventListener("input", () => {
            this.validatePasswordMatch();
        });

        // Also validate when password field changes (user might edit password after filling confirm)
        this.passwordInput.addEventListener("input", () => {
            // Only validate if confirm field has value (user has started filling it)
            if (this.confirmInput.value.trim()) {
                this.validatePasswordMatch();
            }
        });

        // Validate on blur for confirm field
        this.confirmInput.addEventListener("blur", () => {
            if (!RegisterValidation.isTouched(this.confirmInput)) return;
            this.validatePasswordMatch();
        });
    }

    /**
     * Check if passwords match and show/clear error accordingly
     * Also updates submit button state
     */
    validatePasswordMatch() {
        const password = this.passwordInput.value;
        const confirm = this.confirmInput.value;

        // If confirm is empty, don't show mismatch error (required error will handle it)
        if (!confirm) {
            RegisterValidation.clearError("confirmPassword");
            return true;
        }

        // Check if passwords match
        if (password !== confirm) {
            RegisterValidation.showError(
                "confirmPassword",
                REGISTER_MESSAGES.confirmMismatch
            );
            return false;
        }

        // Passwords match - clear error
        RegisterValidation.clearError("confirmPassword");
        return true;
    }

    async handleSendCode() {
        const email = this.emailInput?.value?.trim();

        // Block if cooldown active
        if (this.isCooldownActive()) {
            // Throttle toast: max 1 per second
            const now = Date.now();
            if (now - this.lastCooldownToastAt > 1000) {
                if (window.toast) {
                    window.toast.error(
                        `Tunggu ${this.countdownSeconds} detik untuk kirim ulang.`,
                        3000
                    );
                }
                this.lastCooldownToastAt = now;
            }

            // Re-show dev OTP panel if same email
            if (this.lastOtpCode && this.lastOtpEmail === email) {
                this.showDevOtpPanel(this.lastOtpCode, email);
            }

            return;
        }

        // Block if button is disabled
        if (this.btnGetCode?.disabled) {
            return;
        }

        RegisterValidation.markTouched(this.emailInput);

        if (!email) {
            RegisterValidation.showError(
                "email",
                REGISTER_MESSAGES.emailRequired
            );
            this.emailInput?.focus();
            return;
        }

        if (!RegisterValidation.isValidEmail(email)) {
            RegisterValidation.showError(
                "email",
                REGISTER_MESSAGES.emailInvalid
            );
            this.emailInput?.focus();
            return;
        }

        RegisterValidation.clearError("email");
        this.btnGetCode.disabled = true;
        this.setGetCodeText("Sending...");

        try {
            const csrfToken =
                document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") ||
                document.querySelector('input[name="_token"]')?.value;

            const response = await fetch("/register/send-code", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ email }),
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server returned HTML instead of JSON");
            }

            const result = await response.json();

            if (result.success) {
                // Success toast
                if (window.toast) {
                    window.toast.success(
                        "Kode verifikasi sudah dikirim.",
                        3000
                    );
                }

                // Expiry warning toast (separate from cooldown)
                const expiryMinutes =
                    result.expiry_minutes ?? OTP_CONFIG.OTP_EXPIRY_MINUTES;
                setTimeout(() => {
                    if (window.toast) {
                        window.toast.warning(
                            `Kode berlaku ${expiryMinutes} menit. Jangan bagikan kode ke siapa pun.`,
                            5000
                        );
                    }
                }, 500);

                // Dev mode: store and show OTP
                if (result.otp_code) {
                    this.lastOtpCode = result.otp_code;
                    this.lastOtpEmail = email;
                    this.lastOtpAt = Date.now();
                    this.showDevOtpPanel(result.otp_code, email);
                }

                // Auto-focus verification input
                setTimeout(() => {
                    this.verificationCode?.focus();
                }, 150);

                // Start cooldown (use backend retry_after if provided)
                const cooldownSeconds =
                    result.retry_after ??
                    result.cooldown_seconds ??
                    OTP_CONFIG.RESEND_COOLDOWN_SECONDS;
                this.startCountdown(cooldownSeconds);
            } else {
                this.handleSendCodeError(result);
            }
        } catch (err) {
            if (window.toast) {
                window.toast.error("Terjadi kesalahan. Coba lagi.");
            }
            this.btnGetCode.disabled = false;
            this.setGetCodeText("Get Code");
        }
    }

    handleSendCodeError(result) {
        // Handle rate limit with retry_after
        if (result.retry_after && result.retry_after > 0) {
            this.startCountdown(result.retry_after);
            if (window.toast) {
                window.toast.error(
                    `Tunggu ${result.retry_after} detik untuk kirim ulang.`
                );
            }
            return;
        }

        if (result.errors) {
            Object.keys(result.errors).forEach((field) => {
                const fieldMap = {
                    username: "username",
                    email: "email",
                    password: "password",
                    password_confirmation: "confirmPassword",
                };
                const mappedField = fieldMap[field];
                if (mappedField) {
                    RegisterValidation.showError(
                        mappedField,
                        result.errors[field][0]
                    );
                }
            });

            if (result.errors.email && window.toast) {
                window.toast.error(
                    "Email sudah terdaftar. Coba login atau gunakan email lain."
                );
            }
        } else if (window.toast) {
            window.toast.error(result.message || "Gagal mengirim kode OTP");
        }

        this.btnGetCode.disabled = false;
        this.setGetCodeText("Get Code");
    }

    // Dev OTP Panel (left side, inline with form)
    showDevOtpPanel(code, email) {
        if (!this.devOtpPanel) return;

        // Clear previous timer
        if (this.devOtpTimer) {
            clearTimeout(this.devOtpTimer);
            this.devOtpTimer = null;
        }

        this.devOtpPanel.innerHTML = `
            <div class="dev-otp-panel__content">
                <div class="dev-otp-panel__header">
                    <img src="/assets/icons/info-circle.svg" alt="Info" class="dev-otp-panel__icon">
                    <span class="dev-otp-panel__title">Kode verifikasi</span>
                </div>
                <div class="dev-otp-panel__code">${code}</div>
                <div class="dev-otp-panel__hint">Gunakan kode ini untuk melanjutkan</div>
                <button type="button" class="dev-otp-panel__copy">Salin</button>
            </div>
        `;

        // Show panel
        this.devOtpPanel.classList.remove("hidden");

        // Copy button handler
        const copyBtn = this.devOtpPanel.querySelector(".dev-otp-panel__copy");
        if (copyBtn) {
            copyBtn.addEventListener("click", () => {
                navigator.clipboard.writeText(code);
                if (window.toast) {
                    window.toast.info("Kode disalin.", 3000);
                }
                copyBtn.textContent = "Tersalin";
                setTimeout(() => {
                    copyBtn.textContent = "Salin";
                }, 2000);
            });
        }

        // Auto-hide after 15 seconds
        this.devOtpTimer = setTimeout(() => {
            this.hideDevOtpPanel();
        }, 15000);
    }

    hideDevOtpPanel() {
        if (!this.devOtpPanel) return;

        this.devOtpPanel.classList.add("hidden");
        this.devOtpPanel.innerHTML = "";

        if (this.devOtpTimer) {
            clearTimeout(this.devOtpTimer);
            this.devOtpTimer = null;
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const data = Object.fromEntries(new FormData(this.form).entries());

        if (!this.validateForm(data)) return;

        this.submitBtn.disabled = true;
        this.submitBtn.classList.add("loading");

        try {
            const response = await fetch("/register/verify-code", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || data._token,
                },
                body: JSON.stringify(data),
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server returned HTML instead of JSON");
            }

            const result = await response.json();

            if (result.success) {
                localStorage.removeItem("register_countdown");
                localStorage.removeItem("register_countdown_start");

                // Clear OTP state
                this.lastOtpCode = null;
                this.lastOtpEmail = null;
                this.lastOtpAt = 0;

                // Hide dev panel on success
                this.hideDevOtpPanel();

                if (window.toast) {
                    window.toast.success(
                        "Akun berhasil dibuat. Yuk lanjut onboarding!"
                    );
                }

                setTimeout(() => {
                    window.location.href = result.redirect_url || "/onboarding";
                }, 1000);
            } else {
                this.handleSubmitError(result);
            }
        } catch (error) {
            if (window.toast) {
                window.toast.error("Terjadi kesalahan. Coba lagi ya.");
            }
        } finally {
            this.submitBtn.disabled = false;
            this.submitBtn.classList.remove("loading");
        }
    }

    validateForm(data) {
        let hasErrors = false;
        let firstErrorField = null;

        RegisterValidation.markTouched(this.usernameInput);
        RegisterValidation.markTouched(this.emailInput);
        RegisterValidation.markTouched(this.passwordInput);
        RegisterValidation.markTouched(this.confirmInput);
        RegisterValidation.markTouched(this.verificationCode);

        if (!data.username?.trim()) {
            RegisterValidation.showError(
                "username",
                REGISTER_MESSAGES.username
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.usernameInput;
        } else {
            RegisterValidation.clearError("username");
        }

        if (!data.email?.trim()) {
            RegisterValidation.showError("email", REGISTER_MESSAGES.email);
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.emailInput;
        } else if (!RegisterValidation.isValidEmail(data.email.trim())) {
            RegisterValidation.showError(
                "email",
                REGISTER_MESSAGES.emailInvalid
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.emailInput;
        } else {
            RegisterValidation.clearError("email");
        }

        if (!data.password?.trim()) {
            RegisterValidation.showError(
                "password",
                REGISTER_MESSAGES.password
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.passwordInput;
        } else if (data.password.trim().length < 8) {
            RegisterValidation.showError(
                "password",
                REGISTER_MESSAGES.passwordMin
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.passwordInput;
        } else if (!PASSWORD_REGEX.test(data.password.trim())) {
            RegisterValidation.showError(
                "password",
                REGISTER_MESSAGES.passwordWeak
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.passwordInput;
        } else {
            RegisterValidation.clearError("password");
        }

        if (!data.password_confirmation?.trim()) {
            RegisterValidation.showError(
                "confirmPassword",
                REGISTER_MESSAGES.confirmPassword
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.confirmInput;
        } else if (
            data.password_confirmation.trim() !== data.password?.trim()
        ) {
            RegisterValidation.showError(
                "confirmPassword",
                REGISTER_MESSAGES.confirmMismatch
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.confirmInput;
        } else {
            RegisterValidation.clearError("confirmPassword");
        }

        if (!data.verification_code?.trim()) {
            RegisterValidation.showError(
                "verification",
                REGISTER_MESSAGES.verification
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.verificationCode;
        } else if (data.verification_code.trim().length !== 6) {
            RegisterValidation.showError(
                "verification",
                REGISTER_MESSAGES.verificationLength
            );
            hasErrors = true;
            if (!firstErrorField) firstErrorField = this.verificationCode;
        } else {
            RegisterValidation.clearError("verification");
        }

        if (firstErrorField) firstErrorField.focus();

        return !hasErrors;
    }

    handleSubmitError(result) {
        // Handle OTP verification errors with attempts_left
        if (result.attempts_left !== undefined) {
            let message = result.message || "Kode verifikasi belum cocok.";
            if (result.attempts_left > 0) {
                message = `Kode verifikasi belum cocok. Sisa percobaan: ${result.attempts_left}.`;
            }
            RegisterValidation.showError("verification", message);
            this.verificationCode?.focus();
            return;
        }

        if (result.errors) {
            Object.keys(result.errors).forEach((field) => {
                const fieldMap = {
                    username: "username",
                    email: "email",
                    password: "password",
                    password_confirmation: "confirmPassword",
                    verification_code: "verification",
                };
                const mappedField = fieldMap[field];
                if (mappedField) {
                    RegisterValidation.showError(
                        mappedField,
                        result.errors[field][0]
                    );
                }
            });
        } else {
            // Show general error message for OTP issues
            const message = result.message || "Verifikasi gagal";
            RegisterValidation.showError("verification", message);

            if (window.toast) {
                window.toast.error(message);
            }
        }
    }

    startCountdown(seconds) {
        // Clear existing timer first
        if (this.countdownTimer) {
            clearInterval(this.countdownTimer);
            this.countdownTimer = null;
        }

        // Validate seconds: must be positive and reasonable (max 60)
        const validSeconds = Math.max(
            1,
            Math.min(
                60,
                parseInt(seconds) || OTP_CONFIG.RESEND_COOLDOWN_SECONDS
            )
        );

        this.countdownSeconds = validSeconds;
        localStorage.setItem("register_countdown", validSeconds.toString());
        localStorage.setItem("register_countdown_start", Date.now().toString());

        // Disable button immediately
        if (this.btnGetCode) {
            this.btnGetCode.disabled = true;
        }

        this.updateCountdownUI();

        this.countdownTimer = setInterval(() => {
            this.countdownSeconds--;

            if (this.countdownSeconds <= 0) {
                this.stopCountdown();
            } else {
                this.updateCountdownUI();
                localStorage.setItem(
                    "register_countdown",
                    this.countdownSeconds.toString()
                );
            }
        }, 1000);
    }

    stopCountdown() {
        // Clear timer
        if (this.countdownTimer) {
            clearInterval(this.countdownTimer);
            this.countdownTimer = null;
        }

        // Reset state
        this.countdownSeconds = 0;

        // Enable button
        if (this.btnGetCode) {
            this.btnGetCode.disabled = false;
        }

        this.setGetCodeText("Resend");

        // Clear localStorage
        localStorage.removeItem("register_countdown");
        localStorage.removeItem("register_countdown_start");
    }

    updateCountdownUI() {
        this.btnGetCode.disabled = true;
        this.setGetCodeText(`${this.countdownSeconds}s`);
    }

    restoreCountdown() {
        const savedCountdown = localStorage.getItem("register_countdown");
        const savedStart = localStorage.getItem("register_countdown_start");

        if (savedCountdown && savedStart) {
            const elapsed = Math.floor(
                (Date.now() - parseInt(savedStart)) / 1000
            );
            const remaining = parseInt(savedCountdown) - elapsed;

            if (remaining > 0) {
                this.startCountdown(remaining);
            } else {
                localStorage.removeItem("register_countdown");
                localStorage.removeItem("register_countdown_start");
            }
        }
    }

    setGetCodeText(text) {
        if (this.btnGetCodeText) {
            this.btnGetCodeText.textContent = text;
        } else if (this.btnGetCode) {
            this.btnGetCode.innerText = text;
        }
    }
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
    new RegisterManager();
});
