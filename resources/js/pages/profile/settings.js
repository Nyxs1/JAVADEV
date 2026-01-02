/**
 * Account Settings Page
 * Username availability check with explicit "Check" button
 *
 * State flow:
 * - not_checked: Initial state or after input change
 * - checking: While API call is in progress
 * - available: Username is available (can save)
 * - taken: Username is already taken (cannot save)
 * - same: Username is same as current (no changes needed)
 * - invalid: Username format is invalid
 */

class AccountSettingsManager {
    constructor() {
        this.form = document.getElementById("account-form");
        if (!this.form) return;

        this.usernameInput = document.getElementById("username");
        this.checkBtn = document.getElementById("check-username-btn");
        this.saveBtn = document.getElementById("save-username-btn");
        this.statusContainer = document.getElementById("username-status");
        this.statusIcon = document.getElementById("username-status-icon");
        this.statusText = document.getElementById("username-status-text");

        this.originalUsername = this.usernameInput?.dataset.original || "";
        this.state = "not_checked";
        this.lastCheckedUsername = "";

        this.init();
    }

    init() {
        this.bindEvents();
        this.updateUI();
    }

    bindEvents() {
        // Input change - reset to not_checked
        this.usernameInput?.addEventListener("input", () => {
            this.handleInputChange();
        });

        // Check button click
        this.checkBtn?.addEventListener("click", () => {
            this.checkUsername();
        });

        // Form submit
        this.form?.addEventListener("submit", (e) => {
            this.handleSubmit(e);
        });

        // Enter key in input triggers check
        this.usernameInput?.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                if (!this.checkBtn.disabled) {
                    this.checkUsername();
                }
            }
        });
    }

    handleInputChange() {
        const value = this.usernameInput.value.trim();

        // Reset state when input changes
        if (value !== this.lastCheckedUsername) {
            this.state = "not_checked";
        }

        // Validate format
        if (!this.isValidFormat(value)) {
            this.state = "invalid";
        } else if (value === this.originalUsername) {
            this.state = "same";
        }

        this.updateUI();
    }

    isValidFormat(username) {
        if (!username || username.length < 3 || username.length > 30) {
            return false;
        }
        return /^[a-zA-Z0-9_]+$/.test(username);
    }

    getFormatError(username) {
        if (!username) return "Username is required.";
        if (username.length < 3)
            return "Username must be at least 3 characters.";
        if (username.length > 30)
            return "Username must be at most 30 characters.";
        if (!/^[a-zA-Z0-9_]+$/.test(username))
            return "Only letters, numbers, and underscores allowed.";
        return null;
    }

    async checkUsername() {
        const username = this.usernameInput.value.trim();

        // Validate format first
        const formatError = this.getFormatError(username);
        if (formatError) {
            this.state = "invalid";
            this.updateUI(formatError);
            return;
        }

        // If same as original, no need to check
        if (username === this.originalUsername) {
            this.state = "same";
            this.lastCheckedUsername = username;
            this.updateUI();
            return;
        }

        // Start checking
        this.state = "checking";
        this.updateUI();

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const response = await fetch("/settings/check-username", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ username }),
            });

            const result = await response.json();

            // Check if input value changed during request
            if (this.usernameInput.value.trim() !== username) {
                this.state = "not_checked";
                this.updateUI();
                return;
            }

            this.lastCheckedUsername = username;

            if (result.available) {
                this.state = "available";
            } else {
                this.state = "taken";
            }
        } catch (error) {
            this.state = "not_checked";
            this.showError("Failed to check username. Please try again.");
        }

        this.updateUI();
    }

    updateUI(customError = null) {
        const username = this.usernameInput.value.trim();

        // Update Check button
        const canCheck =
            this.isValidFormat(username) &&
            username !== this.originalUsername &&
            this.state !== "checking";
        this.checkBtn.disabled = !canCheck;
        this.checkBtn.textContent =
            this.state === "checking" ? "Checking..." : "Check";

        // Update Save button
        const canSave = this.state === "available" || this.state === "same";
        this.saveBtn.disabled = !canSave;

        // Update input border
        this.usernameInput.classList.remove(
            "border-green-500",
            "border-red-500",
            "border-slate-300"
        );
        if (this.state === "available") {
            this.usernameInput.classList.add("border-green-500");
        } else if (this.state === "taken" || this.state === "invalid") {
            this.usernameInput.classList.add("border-red-500");
        } else {
            this.usernameInput.classList.add("border-slate-300");
        }

        // Update status message
        this.updateStatusMessage(customError);
    }

    updateStatusMessage(customError = null) {
        const username = this.usernameInput.value.trim();

        // Hide status by default
        this.statusContainer.classList.add("hidden");

        let icon = "";
        let text = "";
        let colorClass = "";

        if (customError) {
            icon = "/assets/icons/x-circle.svg";
            text = customError;
            colorClass = "text-red-600";
        } else {
            switch (this.state) {
                case "not_checked":
                    if (
                        username &&
                        this.isValidFormat(username) &&
                        username !== this.originalUsername
                    ) {
                        icon = "/assets/icons/info-circle.svg";
                        text = "Click Check to verify availability.";
                        colorClass = "text-slate-500";
                    }
                    break;

                case "checking":
                    icon = "";
                    text = "Checking availability...";
                    colorClass = "text-slate-500";
                    break;

                case "available":
                    icon = "/assets/icons/check-circle.svg";
                    text = "Username is available!";
                    colorClass = "text-green-600";
                    break;

                case "taken":
                    icon = "/assets/icons/x-circle.svg";
                    text = "Username is already taken.";
                    colorClass = "text-red-600";
                    break;

                case "same":
                    icon = "/assets/icons/check-circle.svg";
                    text = "This is your current username.";
                    colorClass = "text-slate-600";
                    break;

                case "invalid":
                    const error = this.getFormatError(username);
                    if (error) {
                        icon = "/assets/icons/x-circle.svg";
                        text = error;
                        colorClass = "text-red-600";
                    }
                    break;
            }
        }

        if (text) {
            this.statusContainer.classList.remove("hidden");
            this.statusText.textContent = text;
            this.statusText.className = "text-sm " + colorClass;

            if (icon) {
                this.statusIcon.src = icon;
                this.statusIcon.classList.remove("hidden");
            } else {
                this.statusIcon.classList.add("hidden");
            }
        }
    }

    showError(message) {
        this.statusContainer.classList.remove("hidden");
        this.statusIcon.src = "/assets/icons/x-circle.svg";
        this.statusIcon.classList.remove("hidden");
        this.statusText.textContent = message;
        this.statusText.className = "text-sm text-red-600";
    }

    async handleSubmit(e) {
        e.preventDefault();

        // Block submit if not available or same
        if (this.state !== "available" && this.state !== "same") {
            return;
        }

        // If same as original, no need to submit
        if (this.state === "same") {
            this.showError("No changes to save.");
            return;
        }

        // Show saving state
        this.saveBtn.disabled = true;
        this.saveBtn.textContent = "Saving...";
        this.statusContainer.classList.add("hidden");

        const username = this.usernameInput.value.trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

        try {
            const response = await fetch(this.form.action, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ username }),
            });

            const result = await response.json();

            if (result.success) {
                // Success - reload page or show success
                // For username change, reload is safer to update all UI references
                window.location.reload();
            } else {
                this.showError(result.message || "Failed to update username.");
                this.saveBtn.disabled = false;
                this.saveBtn.textContent = "Save Username";
            }
        } catch (error) {
            console.error(error);
            this.showError("An error occurred. Please try again.");
            this.saveBtn.disabled = false;
            this.saveBtn.textContent = "Save Username";
        }
    }
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
    new AccountSettingsManager();
});
