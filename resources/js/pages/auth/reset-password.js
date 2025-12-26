/**
 * Reset Password Page JavaScript
 * Handles password validation and confirmation matching
 */

class ResetPasswordManager {
    constructor() {
        this.form = document.getElementById("resetPasswordForm");
        this.passwordInput = document.getElementById("password-input");
        this.confirmInput = document.getElementById("confirm-input");
        this.submitBtn = document.getElementById("submitBtn");
        this.passwordToggle = document.getElementById("password-toggle");
        this.confirmToggle = document.getElementById("confirm-toggle");
        this.matchIndicator = document.getElementById("matchIndicator");
        this.requirements = document.getElementById("passwordRequirements");

        if (!this.form) return;

        this.init();
    }

    init() {
        this.initPasswordToggle();
        this.initPasswordValidation();
        this.initConfirmValidation();
        this.initFormSubmit();
        this.clearErrorsOnInput();
    }

    initPasswordToggle() {
        // Password field toggle
        if (this.passwordToggle && this.passwordInput) {
            this.passwordToggle.addEventListener("click", () => {
                const isPassword = this.passwordInput.type === "password";
                this.passwordInput.type = isPassword ? "text" : "password";
                this.passwordToggle.classList.toggle("active", !isPassword);
            });
        }

        // Confirm field toggle
        if (this.confirmToggle && this.confirmInput) {
            this.confirmToggle.addEventListener("click", () => {
                const isPassword = this.confirmInput.type === "password";
                this.confirmInput.type = isPassword ? "text" : "password";
                this.confirmToggle.classList.toggle("active", !isPassword);
            });
        }
    }

    initPasswordValidation() {
        if (!this.passwordInput || !this.requirements) return;

        this.passwordInput.addEventListener("input", () => {
            const password = this.passwordInput.value;

            // Check each requirement
            this.updateRequirement("length", password.length >= 8);
            this.updateRequirement("uppercase", /[A-Z]/.test(password));
            this.updateRequirement("number", /[0-9]/.test(password));
            this.updateRequirement(
                "special",
                /[!@#$%^&*(),.?":{}|<>_\-+=\[\]\\\/`~;']/.test(password)
            );

            // Show requirements when typing
            if (password.length > 0) {
                this.requirements.classList.add("visible");
            } else {
                this.requirements.classList.remove("visible");
            }

            // Re-validate confirm match
            this.validateMatch();
            this.updateSubmitState();
        });
    }

    updateRequirement(name, isValid) {
        const req = this.requirements.querySelector(`[data-req="${name}"]`);
        if (req) {
            req.classList.toggle("valid", isValid);
            req.classList.toggle("invalid", !isValid);
        }
    }

    initConfirmValidation() {
        if (!this.confirmInput) return;

        this.confirmInput.addEventListener("input", () => {
            this.validateMatch();
            this.updateSubmitState();
        });
    }

    validateMatch() {
        if (!this.matchIndicator || !this.confirmInput || !this.passwordInput)
            return;

        const password = this.passwordInput.value;
        const confirm = this.confirmInput.value;
        const matchText = this.matchIndicator.querySelector(".match-text");

        if (confirm.length === 0) {
            this.matchIndicator.classList.remove(
                "visible",
                "match",
                "no-match"
            );
            return;
        }

        this.matchIndicator.classList.add("visible");

        if (password === confirm) {
            this.matchIndicator.classList.add("match");
            this.matchIndicator.classList.remove("no-match");
            if (matchText) matchText.textContent = "Passwords match";
        } else {
            this.matchIndicator.classList.add("no-match");
            this.matchIndicator.classList.remove("match");
            if (matchText) matchText.textContent = "Passwords do not match";
        }
    }

    isPasswordValid() {
        const password = this.passwordInput?.value || "";
        return (
            password.length >= 8 &&
            /[A-Z]/.test(password) &&
            /[0-9]/.test(password) &&
            /[!@#$%^&*(),.?":{}|<>_\-+=\[\]\\\/`~;']/.test(password)
        );
    }

    isConfirmValid() {
        const password = this.passwordInput?.value || "";
        const confirm = this.confirmInput?.value || "";
        return password === confirm && confirm.length > 0;
    }

    updateSubmitState() {
        if (!this.submitBtn) return;

        const isValid = this.isPasswordValid() && this.isConfirmValid();
        this.submitBtn.disabled = !isValid;
    }

    initFormSubmit() {
        if (!this.form) return;

        this.form.addEventListener("submit", (e) => {
            if (!this.isPasswordValid() || !this.isConfirmValid()) {
                e.preventDefault();
                return;
            }

            // Show loading state
            if (this.submitBtn) {
                this.submitBtn.disabled = true;
                this.submitBtn.classList.add("loading");
            }
        });
    }

    clearErrorsOnInput() {
        const errorBanner = document.getElementById("authErrorBanner");
        const inputs = this.form?.querySelectorAll("input");

        if (!errorBanner || !inputs) return;

        inputs.forEach((input) => {
            input.addEventListener(
                "input",
                () => {
                    errorBanner.style.display = "none";
                },
                { once: true }
            );
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new ResetPasswordManager();
});

export default ResetPasswordManager;
