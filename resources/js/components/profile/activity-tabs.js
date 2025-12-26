/**
 * Activity Tabs Component
 * Handles tab switching and privacy toggle for profile activity sections
 */

class ActivityTabs {
    constructor() {
        this.container = document.getElementById("activity-tabs-container");
        if (!this.container) return;

        this.tabs = this.container.querySelectorAll(".activity-tab");
        this.contents = this.container.querySelectorAll(".activity-content");
        this.visibilityIcons =
            this.container.querySelectorAll(".visibility-icon");
        this.isSaving = false;

        this.init();
    }

    init() {
        this.initTabSwitching();
        this.initVisibilityToggle();
    }

    initTabSwitching() {
        this.tabs.forEach((tab) => {
            tab.addEventListener("click", (e) => {
                // Ignore clicks on visibility icon
                if (e.target.classList.contains("visibility-icon")) return;

                const targetTab = tab.dataset.tab;
                this.switchTab(targetTab);
            });
        });
    }

    switchTab(targetTab) {
        // Update tab styles
        this.tabs.forEach((t) => {
            t.classList.remove("border-blue-500", "text-blue-600");
            t.classList.add("border-transparent", "text-slate-600");
            t.setAttribute("aria-selected", "false");
        });

        const activeTab = this.container.querySelector(
            `[data-tab="${targetTab}"]`
        );
        if (activeTab) {
            activeTab.classList.remove("border-transparent", "text-slate-600");
            activeTab.classList.add("border-blue-500", "text-blue-600");
            activeTab.setAttribute("aria-selected", "true");
        }

        // Update content visibility
        this.contents.forEach((content) => content.classList.add("hidden"));
        const targetContent = document.getElementById("tab-" + targetTab);
        if (targetContent) targetContent.classList.remove("hidden");
    }

    initVisibilityToggle() {
        this.visibilityIcons.forEach((icon) => {
            icon.addEventListener("click", async (e) => {
                e.stopPropagation();

                if (this.isSaving) return;

                const tabType = icon.dataset.tab;
                const currentlyPublic = icon.dataset.public === "true";
                const newValue = !currentlyPublic;

                await this.savePrivacy(icon, tabType, newValue);
            });
        });
    }

    async savePrivacy(icon, tabType, newValue) {
        this.isSaving = true;
        icon.style.opacity = "0.3";

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const response = await fetch("/profile/privacy", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    activity_type: tabType,
                    is_public: newValue,
                }),
            });

            const result = await response.json();

            if (result.success) {
                // Update icon - use assets/icons path
                icon.src =
                    "/assets/icons/" +
                    (newValue ? "globe-blue" : "lock-gray") +
                    ".svg";
                icon.title = newValue
                    ? "Visible to everyone"
                    : "Only visible to you";
                icon.dataset.public = newValue.toString();
                this.showFeedback("success", "Saved!");
            } else {
                this.showFeedback("error", "Failed to save.");
            }
        } catch (error) {
            console.error("Privacy save error:", error);
            this.showFeedback("error", "Failed to save.");
        } finally {
            this.isSaving = false;
            icon.style.opacity = "1";
        }
    }

    showFeedback(type, message) {
        const feedback = document.createElement("div");
        feedback.className = `fixed bottom-4 right-4 px-3 py-2 rounded-lg shadow-lg z-50 text-sm ${
            type === "success"
                ? "bg-green-600 text-white"
                : "bg-red-600 text-white"
        }`;
        feedback.textContent = message;
        document.body.appendChild(feedback);
        setTimeout(() => feedback.remove(), 2000);
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new ActivityTabs();
});

export default ActivityTabs;
