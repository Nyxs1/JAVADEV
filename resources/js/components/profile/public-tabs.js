export class PublicTabs {
    constructor() {
        this.container = document.getElementById("public-tabs-container");
        if (!this.container) return;

        this.tabs = this.container.querySelectorAll(".public-tab");
        this.contents = this.container.querySelectorAll(".public-content");

        // Guard: if no tabs found, do nothing
        if (this.tabs.length === 0) return;

        this.init();
    }

    init() {
        this.initTabSwitching();
        
        // Restore active tab from localStorage if available
        const savedTab = localStorage.getItem('javadev_active_public_tab');
        if (savedTab) {
            const tabToActivate = this.container.querySelector(`[data-tab="${savedTab}"]`);
            if (tabToActivate) {
                this.switchTab(savedTab);
            }
        }
    }

    initTabSwitching() {
        this.tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                const targetTab = tab.dataset.tab;
                if (!targetTab) return;
                this.switchTab(targetTab);
                
                // Save state
                localStorage.setItem('javadev_active_public_tab', targetTab);
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
 * PERSISTS STATE via LocalStorage
 */
class PrivateDetailsToggle {
    constructor() {
        this.toggleBtn = document.querySelector("[data-toggle-private]");
        this.content = document.querySelector("[data-private-content]");
        this.icon = document.querySelector("[data-toggle-icon]");
        this.STORAGE_KEY = 'javadev_private_details_open';

        if (!this.toggleBtn || !this.content) return;

        // Load initial state
        const savedState = localStorage.getItem(this.STORAGE_KEY);
        this.isOpen = savedState === 'true';
        
        // Apply initial state without animation
        if (this.isOpen) {
            this.content.classList.remove("hidden");
            this.icon?.classList.add("rotate-180");
        }

        this.init();
    }

    init() {
        this.toggleBtn.addEventListener("click", (e) => {
            e.preventDefault(); 
            this.toggle();
        });
    }

    toggle() {
        this.isOpen = !this.isOpen;
        localStorage.setItem(this.STORAGE_KEY, this.isOpen);

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
    // Only init if containers exist
    if (document.getElementById("public-tabs-container")) {
        new PublicTabs();
    }
    
    if (document.querySelector("[data-toggle-private]")) {
        new PrivateDetailsToggle();
    }
});
