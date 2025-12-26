/**
 * My Events Tabs Module
 * Handles tab switching for My Events page
 */

class MyEventsTabs {
    constructor() {
        this.root = document.querySelector("[data-my-events-tabs]");
        if (!this.root) return;

        this.tabs = this.root.querySelectorAll(".my-events__tab");
        this.panels = this.root.querySelectorAll(".my-events__panel");

        if (this.tabs.length === 0 || this.panels.length === 0) return;

        this.init();
    }

    init() {
        this.bindTabClicks();
        this.restoreFromHash();

        window.addEventListener("hashchange", () => this.restoreFromHash());
    }

    bindTabClicks() {
        this.tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                const tabId = tab.dataset.tab;
                this.switchTab(tabId);
                this.updateHash(tabId);
            });
        });
    }

    switchTab(tabId) {
        // Update tab buttons
        this.tabs.forEach((tab) => {
            tab.classList.toggle("active", tab.dataset.tab === tabId);
        });

        // Update panels
        this.panels.forEach((panel) => {
            const panelId = panel.id.replace("panel-", "");
            panel.classList.toggle("active", panelId === tabId);
        });
    }

    updateHash(tabId) {
        history.replaceState(null, "", `#${tabId}`);
    }

    restoreFromHash() {
        const hash = window.location.hash.replace("#", "");
        const validTabs = Array.from(this.tabs).map((t) => t.dataset.tab);

        if (hash && validTabs.includes(hash)) {
            this.switchTab(hash);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new MyEventsTabs();
});

export default MyEventsTabs;
