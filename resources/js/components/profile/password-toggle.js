/**
 * Password Toggle Component
 * Handles show/hide password functionality for password inputs
 */

class PasswordToggle {
    constructor() {
        this.toggleButtons = document.querySelectorAll(".password-toggle");
        if (!this.toggleButtons.length) return;

        this.init();
    }

    init() {
        this.toggleButtons.forEach((btn) => {
            btn.addEventListener("click", () => {
                const targetId = btn.getAttribute("data-target");
                const input = document.getElementById(targetId);

                if (!input) return;

                // Toggle input type
                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";

                // Toggle button active state (for styling)
                btn.classList.toggle("active", !isPassword);
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new PasswordToggle();
});

export default PasswordToggle;
