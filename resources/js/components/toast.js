/**
 * Toast Manager - Reusable notification system
 * API: window.toast.success(msg), window.toast.error(msg), window.toast.info(msg), window.toast.warning(msg)
 */

class ToastManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        if (!document.getElementById("toast-container")) {
            this.container = document.createElement("div");
            this.container.id = "toast-container";
            this.container.className = "toast-container";
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById("toast-container");
        }
    }

    /**
     * Show toast notification
     * @param {string} message - Toast message
     * @param {string} type - 'success' | 'error' | 'info' | 'warning'
     * @param {number} duration - Auto-dismiss duration in ms (default 4000)
     */
    show(message, type = "info", duration = 4000) {
        if (!this.container) this.init();

        const toastEl = document.createElement("div");
        toastEl.className = `toast toast-${type}`;

        const iconPath = this.getIconPath(type);

        toastEl.innerHTML = `
            <div class="toast-icon">
                <img src="${iconPath}" alt="${type}" class="toast-icon-img">
            </div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" aria-label="Close" type="button">
                <img src="/assets/icons/close-icon.svg" alt="Close" class="toast-close-img">
            </button>
        `;

        const closeBtn = toastEl.querySelector(".toast-close");
        closeBtn.addEventListener("click", () => this.dismiss(toastEl));

        this.container.appendChild(toastEl);

        requestAnimationFrame(() => {
            toastEl.classList.add("toast-show");
        });

        if (duration > 0) {
            setTimeout(() => this.dismiss(toastEl), duration);
        }

        return toastEl;
    }

    dismiss(toastEl) {
        if (!toastEl || !toastEl.parentNode) return;

        toastEl.classList.remove("toast-show");
        toastEl.classList.add("toast-hide");

        setTimeout(() => {
            if (toastEl.parentNode) {
                toastEl.parentNode.removeChild(toastEl);
            }
        }, 300);
    }

    getIconPath(type) {
        const icons = {
            success: "/assets/icons/check-circle.svg",
            error: "/assets/icons/x-circle.svg",
            info: "/assets/icons/info-circle.svg",
            warning: "/assets/icons/warning.svg",
        };
        return icons[type] || icons.info;
    }

    success(message, duration = 4000) {
        return this.show(message, "success", duration);
    }

    error(message, duration = 5000) {
        return this.show(message, "error", duration);
    }

    info(message, duration = 4000) {
        return this.show(message, "info", duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, "warning", duration);
    }
}

// Create global instance when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    window.toast = new ToastManager();
});

// Also create immediately if DOM already loaded
if (document.readyState !== "loading") {
    window.toast = new ToastManager();
}
