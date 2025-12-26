/**
 * Navbar Avatar Dropdown Component
 * Smooth slide-down animation with dynamic caret positioning
 */

class NavbarDropdown {
    // Micro offset for pixel-perfect caret alignment (adjust if needed)
    static CARET_OFFSET_PX = 0;

    constructor() {
        this.button = document.getElementById("avatar-button");
        this.menu = document.getElementById("avatar-menu");
        this.dropdownContent = this.menu?.querySelector(
            ".avatar-dropdown-content"
        );
        this.avatarCircle = document.getElementById("avatar-circle");
        this.isOpen = false;

        if (
            this.button &&
            this.menu &&
            this.dropdownContent &&
            this.avatarCircle
        ) {
            this.init();
        }
    }

    init() {
        // Click toggle
        this.button.addEventListener("click", (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Outside click close
        document.addEventListener("click", (e) => {
            if (
                !this.button.contains(e.target) &&
                !this.menu.contains(e.target)
            ) {
                this.close();
            }
        });

        // ESC key close
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && this.isOpen) {
                this.close();
                this.button.focus();
            }
        });

        // Recalculate caret on resize
        window.addEventListener("resize", () => {
            if (this.isOpen) {
                this.updateCaretPosition();
            }
        });

        // Menu item keyboard navigation
        this.setupKeyboardNavigation();
    }

    /**
     * Calculate caret X position to align with avatar circle center
     * Dropdown stays fixed (right-aligned), only caret moves inside
     */
    updateCaretPosition() {
        // Get avatar circle bounding box (the actual circle, not the button)
        const circleRect = this.avatarCircle.getBoundingClientRect();
        const dropdownRect = this.dropdownContent.getBoundingClientRect();

        // Avatar circle center X (absolute screen position)
        const avatarCenterX = circleRect.left + circleRect.width / 2;

        // Caret X relative to dropdown left edge
        let caretX =
            avatarCenterX - dropdownRect.left + NavbarDropdown.CARET_OFFSET_PX;

        // Clamp to keep caret inside dropdown (16px margin from edges)
        const minX = 16;
        const maxX = this.dropdownContent.offsetWidth - 16;
        caretX = Math.max(minX, Math.min(maxX, caretX));

        // Set CSS variable for caret position
        this.dropdownContent.style.setProperty("--caret-x", `${caretX}px`);
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.menu.classList.add("open");
        this.button.setAttribute("aria-expanded", "true");
        this.isOpen = true;

        // Update caret position after dropdown is visible
        requestAnimationFrame(() => {
            this.updateCaretPosition();
        });

        // Focus first menu item after animation
        setTimeout(() => {
            const firstItem = this.menu.querySelector("a, button");
            if (firstItem) {
                firstItem.focus();
            }
        }, 50);
    }

    close() {
        this.menu.classList.remove("open");
        this.button.setAttribute("aria-expanded", "false");
        this.isOpen = false;
    }

    setupKeyboardNavigation() {
        const menuItems = this.menu.querySelectorAll("a, button");

        menuItems.forEach((item, index) => {
            item.addEventListener("keydown", (e) => {
                switch (e.key) {
                    case "ArrowDown":
                        e.preventDefault();
                        const nextIndex = (index + 1) % menuItems.length;
                        menuItems[nextIndex].focus();
                        break;

                    case "ArrowUp":
                        e.preventDefault();
                        const prevIndex =
                            index === 0 ? menuItems.length - 1 : index - 1;
                        menuItems[prevIndex].focus();
                        break;

                    case "Tab":
                        if (e.shiftKey && index === 0) {
                            this.close();
                        } else if (
                            !e.shiftKey &&
                            index === menuItems.length - 1
                        ) {
                            this.close();
                        }
                        break;
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new NavbarDropdown();
});

export default NavbarDropdown;
