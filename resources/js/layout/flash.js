/**
 * Flash Message Handler
 * Reads flash data from DOM and triggers toast notifications
 */

class FlashHandler {
    constructor() {
        this.init();
    }

    init() {
        // Look for flash data container
        const flashData = document.getElementById("flash-data");
        if (!flashData) return;

        const success = flashData.dataset.success;
        const error = flashData.dataset.error;
        const info = flashData.dataset.info;

        if (success && window.toast) {
            window.toast.success(success);
        }
        if (error && window.toast) {
            window.toast.error(error);
        }
        if (info && window.toast) {
            window.toast.info(info);
        }

        // Remove the data container after processing
        flashData.remove();
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new FlashHandler();
});

export default FlashHandler;
