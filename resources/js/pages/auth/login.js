/**
 * Login Page Manager
 * Handles form validation with touched state, password toggle, and social login
 */

// Validation Messages (English)
const LOGIN_MESSAGES = {
    login: "Email or username is required.",
    password: "Password is required.",
};

// Reusable validation helpers
const ValidationHelper = {
    markTouched(input) {
        if (input) input.dataset.touched = "1";
    },

    isTouched(input) {
        return input?.dataset.touched === "1";
    },

    showError(input, message) {
        const wrapper = input.closest(".auth-input");
        if (!wrapper) return;

        wrapper.classList.add("error");

        const fieldContainer = wrapper.parentNode;
        const existingError = fieldContainer.querySelector(".auth-error");
        if (existingError) existingError.remove();

        const errorDiv = document.createElement("div");
        errorDiv.className = "auth-error";
        errorDiv.textContent = message;

        wrapper.insertAdjacentElement("afterend", errorDiv);
    },

    clearError(input) {
        const wrapper = input.closest(".auth-input");
        if (!wrapper) return;

        wrapper.classList.remove("error");

        const errorDiv = wrapper.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains("auth-error")) {
            errorDiv.remove();
        }
    },

    validateRequired(input, message) {
        if (!input?.value?.trim()) {
            this.showError(input, message);
            return false;
        }
        this.clearError(input);
        return true;
    },

    bindRequiredField(input, message) {
        if (!input) return;

        // Focus: mark as touched
        input.addEventListener("focus", () => {
            this.markTouched(input);
        });

        // Blur: validate if touched and empty
        input.addEventListener("blur", () => {
            if (this.isTouched(input) && !input.value.trim()) {
                this.showError(input, message);
            }
        });

        // Input: clear error if value exists
        input.addEventListener("input", () => {
            if (input.value.trim()) {
                this.clearError(input);
            }
        });
    },
};

class LoginManager {
    constructor() {
        // Guard: Only run on login page
        this.form = document.getElementById("loginForm");
        if (!this.form) return;

        this.loginInput = document.getElementById("login-input");
        this.passwordInput = document.getElementById("password-input");
        this.eyeIcon = document.getElementById("eye-icon");
        this.toggleBtn = document.getElementById("password-toggle");
        this.submitBtn = document.getElementById("submitBtn");
        this.authErrorBanner = document.getElementById("authErrorBanner");
        this.shownSecurityTip = false;

        this.init();
    }

    init() {
        this.initPasswordToggle();
        this.initInputValueDetection();
        this.initAutofillSync();
        this.initRequiredValidation();
        this.initFormValidation();
        this.initSocialLogin();
        this.initKeyboardNavigation();
        this.initSecurityTip();
        this.initAuthErrorClear();
    }

    /**
     * Sync autofill values - browsers may fill without triggering input events
     */
    initAutofillSync() {
        const syncValues = () => {
            const inputs = this.form.querySelectorAll(".auth-input input");
            inputs.forEach((input) => {
                const wrapper = input.closest(".auth-input");
                if (wrapper && input.value.trim() !== "") {
                    wrapper.classList.add("has-value");
                }
            });
        };

        // Sync after short delay (autofill timing varies)
        setTimeout(syncValues, 150);
        setTimeout(syncValues, 500);

        // Also sync on window focus (user returns to tab)
        window.addEventListener("focus", syncValues);
    }

    /**
     * Clear auth error banner and field error states when user starts typing
     */
    initAuthErrorClear() {
        const inputs = [this.loginInput, this.passwordInput];

        inputs.forEach((input) => {
            if (!input) return;

            input.addEventListener("input", () => {
                // Remove error banner
                if (this.authErrorBanner) {
                    this.authErrorBanner.remove();
                    this.authErrorBanner = null;
                }

                // Remove error state from both fields
                const loginWrapper = document.getElementById("loginInput");
                const passwordWrapper =
                    document.getElementById("passwordWrapper");

                if (loginWrapper) loginWrapper.classList.remove("error");
                if (passwordWrapper) passwordWrapper.classList.remove("error");
            });
        });
    }

    initPasswordToggle() {
        if (!this.toggleBtn || !this.passwordInput || !this.eyeIcon) return;

        this.toggleBtn.addEventListener("click", (e) => {
            e.preventDefault();
            this.togglePasswordVisibility();
        });
    }

    togglePasswordVisibility() {
        const isPassword = this.passwordInput.type === "password";

        this.passwordInput.type = isPassword ? "text" : "password";
        this.eyeIcon.src = isPassword
            ? "/assets/icons/eye-off-icon.svg"
            : "/assets/icons/eye-icon.svg";
        this.eyeIcon.alt = isPassword ? "Hide Password" : "Show Password";
        this.toggleBtn.classList.toggle("active", isPassword);
    }

    initInputValueDetection() {
        const inputs = this.form.querySelectorAll(".auth-input input");

        // Check for autofill after short delay
        setTimeout(() => {
            inputs.forEach((input) => {
                if (input.value !== "") {
                    input.closest(".auth-input")?.classList.add("has-value");
                }
            });
        }, 100);

        inputs.forEach((input) => {
            const updateState = () => {
                const wrapper = input.closest(".auth-input");
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
        // Bind touched validation
        ValidationHelper.bindRequiredField(
            this.loginInput,
            LOGIN_MESSAGES.login
        );
        ValidationHelper.bindRequiredField(
            this.passwordInput,
            LOGIN_MESSAGES.password
        );
    }

    initFormValidation() {
        this.form.addEventListener("submit", (e) => {
            // Force sync autofill values before validation
            this.syncAutofillValues();

            if (!this.validateForm()) {
                e.preventDefault();
                return false;
            }

            // Loading state
            if (this.submitBtn) {
                this.submitBtn.disabled = true;
                this.submitBtn.classList.add("loading");

                setTimeout(() => {
                    if (this.submitBtn.classList.contains("loading")) {
                        this.submitBtn.disabled = false;
                        this.submitBtn.classList.remove("loading");
                    }
                }, 5000);
            }
        });
    }

    /**
     * Force read current DOM values (handles autofill edge cases)
     */
    syncAutofillValues() {
        const inputs = this.form.querySelectorAll(".auth-input input");
        inputs.forEach((input) => {
            const wrapper = input.closest(".auth-input");
            if (wrapper) {
                if (input.value.trim() !== "") {
                    wrapper.classList.add("has-value");
                } else {
                    wrapper.classList.remove("has-value");
                }
            }
        });
    }

    validateForm() {
        let isValid = true;
        let firstError = null;

        // Mark all as touched on submit
        ValidationHelper.markTouched(this.loginInput);
        ValidationHelper.markTouched(this.passwordInput);

        if (
            !ValidationHelper.validateRequired(
                this.loginInput,
                LOGIN_MESSAGES.login
            )
        ) {
            isValid = false;
            if (!firstError) firstError = this.loginInput;
        }

        if (
            !ValidationHelper.validateRequired(
                this.passwordInput,
                LOGIN_MESSAGES.password
            )
        ) {
            isValid = false;
            if (!firstError) firstError = this.passwordInput;
        }

        // Focus first error field
        if (firstError) firstError.focus();

        return isValid;
    }

    initSocialLogin() {
        const googleBtn = document.getElementById("google-login");
        const githubBtn = document.getElementById("github-login");

        if (googleBtn) {
            googleBtn.addEventListener("click", () => {
                this.showSocialNotification("Google");
            });
        }

        if (githubBtn) {
            githubBtn.addEventListener("click", () => {
                this.showSocialNotification("GitHub");
            });
        }
    }

    showSocialNotification(provider) {
        if (window.toast) {
            window.toast.info(`${provider} login coming soon`);
        }
    }

    initKeyboardNavigation() {
        const inputs = this.form.querySelectorAll(
            'input:not([type="checkbox"])'
        );

        inputs.forEach((input, index) => {
            input.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();

                    if (index === inputs.length - 1) {
                        // Last input - submit the form
                        this.form.requestSubmit();
                    } else {
                        const nextInput = inputs[index + 1];
                        if (nextInput) nextInput.focus();
                    }
                }
            });
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                this.clearAllErrors();
            }
        });
    }

    clearAllErrors() {
        const errors = this.form.querySelectorAll(".auth-error");
        const errorInputs = this.form.querySelectorAll(".auth-input.error");

        errors.forEach((error) => error.remove());
        errorInputs.forEach((input) => input.classList.remove("error"));
    }

    initSecurityTip() {
        if (this.passwordInput) {
            this.passwordInput.addEventListener(
                "focus",
                () => {
                    if (!this.shownSecurityTip && window.toast) {
                        this.shownSecurityTip = true;
                        window.toast.warning(
                            "Never share your password with anyone.",
                            5000
                        );
                    }
                },
                { once: true }
            );
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new LoginManager();
});

// Reset loading state on page show (back button)
window.addEventListener("pageshow", () => {
    const submitBtn = document.querySelector(".auth-btn.loading");
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove("loading");
    }
});

export default LoginManager;
