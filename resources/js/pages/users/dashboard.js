/**
 * Users Dashboard JavaScript
 */

document.addEventListener("DOMContentLoaded", () => {
    initCreateEventModal();
});

/**
 * Initialize create event modal
 */
function initCreateEventModal() {
    const modal = document.getElementById("create-event-modal");
    const openBtn = document.getElementById("create-event-btn");

    if (!modal) return;

    // Open modal
    if (openBtn) {
        openBtn.addEventListener("click", () => {
            modal.classList.remove("hidden");
            document.body.style.overflow = "hidden";
        });
    }

    // Close modal handlers
    const closeModal = () => {
        modal.classList.add("hidden");
        document.body.style.overflow = "";
    };

    // Close on backdrop click
    const backdrop = modal.querySelector("[data-modal-backdrop]");
    if (backdrop) {
        backdrop.addEventListener("click", closeModal);
    }

    // Close on close button click
    const closeButtons = modal.querySelectorAll("[data-modal-close]");
    closeButtons.forEach((btn) => {
        btn.addEventListener("click", closeModal);
    });

    // Close on escape key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !modal.classList.contains("hidden")) {
            closeModal();
        }
    });
}
