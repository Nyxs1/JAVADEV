/**
 * Logout Notification Handler
 * Handles the slide-in notification after logout with auto-dismiss
 */

class LogoutNotification {
    constructor() {
        this.notification = document.getElementById("logout-notification");
        if (!this.notification) return;

        this.init();
    }

    init() {
        // Bind close function globally for onclick
        window.closeLogoutNotification = () => this.close();

        // Slide-in animation
        this.notification.style.transform = "translateX(100%)";
        this.notification.style.transition = "all 0.3s ease-out";

        setTimeout(() => {
            this.notification.style.transform = "translateX(0)";
        }, 100);

        // Auto close after 8 seconds
        setTimeout(() => {
            this.close();
        }, 8000);
    }

    close() {
        if (!this.notification) return;

        this.notification.style.transform = "translateX(100%)";
        this.notification.style.opacity = "0";

        setTimeout(() => {
            this.notification.remove();
        }, 300);
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new LogoutNotification();
});

export default LogoutNotification;
