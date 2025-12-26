/**
 * Public Tabs Component
 * Handles tab switching for public profile sections (Portfolio, Courses, Discussions)
 */

class PublicTabs {
    constructor() {
        this.container = document.getElementById("public-tabs-container");
        if (!this.container) return;

        this.tabs = this.container.querySelectorAll(".public-tab");
        this.contents = this.container.querySelectorAll(".public-content");

        this.init();
    }

    init() {
        this.initTabSwitching();
    }

    initTabSwitching() {
        this.tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
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
        const targetContent = document.getElementById(
            "public-tab-" + targetTab
        );
        if (targetContent) targetContent.classList.remove("hidden");
    }
}

/**
 * Private Details Toggle
 * Handles collapsible private details section on profile page
 */
class PrivateDetailsToggle {
    constructor() {
        this.toggleBtn = document.querySelector("[data-toggle-private]");
        this.content = document.querySelector("[data-private-content]");
        this.icon = document.querySelector("[data-toggle-icon]");

        if (!this.toggleBtn || !this.content) return;

        this.isOpen = false;
        this.init();
    }

    init() {
        this.toggleBtn.addEventListener("click", () => this.toggle());
    }

    toggle() {
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            this.content.classList.remove("hidden");
            this.icon?.classList.add("rotate-180");
        } else {
            this.content.classList.add("hidden");
            this.icon?.classList.remove("rotate-180");
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new PublicTabs();
    new PrivateDetailsToggle();
});

export default PublicTabs;
