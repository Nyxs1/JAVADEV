/**
 * Event Detail Tabs Module
 * Handles tab switching with URL hash persistence
 */

class EventDetailTabs {
    constructor() {
        this.root = document.querySelector("[data-event-tabs]");
        if (!this.root) return;

        this.tabs = this.root.querySelectorAll("[data-tab]");
        this.panels = this.root.querySelectorAll(".event-panel");

        if (this.tabs.length === 0 || this.panels.length === 0) return;

        this.init();
    }

    init() {
        this.bindTabClicks();
        this.bindTabTriggers();
        this.restoreFromHash();
        this.initRatingInput();
        this.initRequirementsForm();
        this.initChecklist();

        // Listen for hash changes (back/forward navigation)
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

    /**
     * Bind external tab trigger buttons (e.g., "Leave a Review" button)
     */
    bindTabTriggers() {
        const triggers = this.root.querySelectorAll("[data-tab-trigger]");
        triggers.forEach((trigger) => {
            trigger.addEventListener("click", () => {
                const tabId = trigger.dataset.tabTrigger;
                this.switchTab(tabId);
                this.updateHash(tabId);
            });
        });
    }

    switchTab(tabId) {
        // Update tab buttons
        this.tabs.forEach((tab) => {
            const isActive = tab.dataset.tab === tabId;
            tab.classList.toggle("active", isActive);
            tab.setAttribute("aria-selected", isActive ? "true" : "false");
        });

        // Update panels
        this.panels.forEach((panel) => {
            const panelId = panel.id.replace("tab-", "");
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
        } else {
            // Default to first tab
            const firstTab = this.tabs[0]?.dataset.tab;
            if (firstTab) {
                this.switchTab(firstTab);
            }
        }
    }

    /**
     * Initialize star rating input for review form
     */
    initRatingInput() {
        const ratingContainer = this.root.querySelector("[data-rating-input]");
        if (!ratingContainer) return;

        const stars = ratingContainer.querySelectorAll(
            ".event-reviews__form-star"
        );
        const hiddenInput = ratingContainer.querySelector("#rating-input");

        if (!stars.length || !hiddenInput) return;

        let currentRating = 0;

        const updateStars = (rating, isHover = false) => {
            stars.forEach((star, index) => {
                const img = star.querySelector("img");
                const filled = img.dataset.filled;
                const empty = img.dataset.empty;

                if (index < rating) {
                    img.src = filled;
                } else if (!isHover && index < currentRating) {
                    img.src = filled;
                } else {
                    img.src = empty;
                }
            });
        };

        stars.forEach((star, index) => {
            star.addEventListener("mouseenter", () => {
                updateStars(index + 1, true);
            });

            star.addEventListener("mouseleave", () => {
                updateStars(currentRating);
            });

            star.addEventListener("click", () => {
                currentRating = index + 1;
                hiddenInput.value = currentRating;
                updateStars(currentRating);
            });
        });
    }

    /**
     * Initialize requirements edit form repeater functionality
     */
    initRequirementsForm() {
        const form = document.querySelector("[data-requirements-form]");
        if (!form) return;

        // Add item buttons
        form.querySelectorAll("[data-add-item]").forEach((btn) => {
            btn.addEventListener("click", () => {
                const fieldName = btn.dataset.addItem;
                const repeater = form.querySelector(
                    `[data-repeater="${fieldName}"]`
                );
                if (!repeater) return;

                const lastItem = repeater.querySelector(
                    ".requirements-edit__repeater-item:last-child"
                );
                if (!lastItem) return;

                const newItem = lastItem.cloneNode(true);
                const input = newItem.querySelector("input");
                if (input) {
                    input.value = "";
                }

                // Re-bind remove button
                const removeBtn = newItem.querySelector("[data-remove-item]");
                if (removeBtn) {
                    removeBtn.addEventListener("click", () => {
                        this.removeRepeaterItem(newItem, repeater);
                    });
                }

                repeater.appendChild(newItem);
                input?.focus();
            });
        });

        // Remove item buttons
        form.querySelectorAll("[data-remove-item]").forEach((btn) => {
            btn.addEventListener("click", () => {
                const item = btn.closest(".requirements-edit__repeater-item");
                const repeater = btn.closest(".requirements-edit__repeater");
                this.removeRepeaterItem(item, repeater);
            });
        });
    }

    removeRepeaterItem(item, repeater) {
        const items = repeater.querySelectorAll(
            ".requirements-edit__repeater-item"
        );
        // Keep at least one item
        if (items.length > 1) {
            item.remove();
        } else {
            // Clear the input instead of removing
            const input = item.querySelector("input");
            if (input) input.value = "";
        }
    }

    /**
     * Initialize checklist toggle functionality
     */
    initChecklist() {
        const checklist = this.root.querySelector("[data-checklist]");
        if (!checklist) return;

        const checkboxes = checklist.querySelectorAll(
            ".event-requirements__checkbox-input"
        );

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", async () => {
                const url = checkbox.dataset.toggleUrl;
                const textEl = checkbox.parentElement.querySelector(
                    ".event-requirements__check-text"
                );

                // Optimistic UI update
                textEl.classList.toggle("is-checked", checkbox.checked);

                try {
                    const response = await fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        // Revert on error
                        checkbox.checked = !checkbox.checked;
                        textEl.classList.toggle("is-checked", checkbox.checked);

                        if (data.message) {
                            alert(data.message);
                        }
                    }
                } catch (error) {
                    // Revert on network error
                    checkbox.checked = !checkbox.checked;
                    textEl.classList.toggle("is-checked", checkbox.checked);
                    console.error("Checklist toggle failed:", error);
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new EventDetailTabs();
});

export default EventDetailTabs;
