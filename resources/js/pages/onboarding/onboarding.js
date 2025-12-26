/**
 * Onboarding Page JavaScript
 * Handles multi-step form navigation and validation
 */

class OnboardingManager {
    constructor() {
        // Guard: Only run on onboarding page
        this.form = document.getElementById("onboarding-form");
        if (!this.form) return; // Exit if not on onboarding page

        // Check if server wants us to start at a specific step (e.g., after validation error)
        // Read from data attribute on onboarding-root element
        const rootEl = document.getElementById("onboarding-root");
        const startStep = rootEl?.dataset.startStep
            ? parseInt(rootEl.dataset.startStep, 10)
            : 1;

        this.currentStep = startStep;
        this.totalSteps = 2;
        this.completedSteps = new Set(); // Track completed steps
        this.prevBtn = document.getElementById("prev-btn");
        this.nextBtn = document.getElementById("next-btn");
        this.submitBtn = document.getElementById("submit-btn");

        // If starting at step 2, mark step 1 as completed
        if (startStep === 2) {
            this.completedSteps.add(1);
        }

        this.init();
    }

    init() {
        // Guard: Don't init if form doesn't exist
        if (!this.form) return;

        // Navigation buttons
        if (this.nextBtn) {
            this.nextBtn.addEventListener("click", () => this.nextStep());
        }

        if (this.prevBtn) {
            this.prevBtn.addEventListener("click", () => this.prevStep());
        }

        // Form submit handler for loading state
        if (this.form && this.submitBtn) {
            this.form.addEventListener("submit", (e) => this.handleSubmit(e));
        }

        // Middle name toggle
        this.initMiddleNameToggle();

        // Birth date age calculation
        this.initAgeCalculation();

        // Profile picture preview
        this.initProfilePicture();

        // Birth date modal picker
        this.initBirthDateModal();

        // Focus areas selection
        this.initFocusAreas();

        // Role selection
        this.initRoleSelection();

        // Form validation
        this.initValidation();

        // Update UI for current step
        this.updateUI();
        this.updateProgress();

        // this.initFocusAreasUI();
        this.initRoleSelectUI();
    }

    handleSubmit(e) {
        // Validate before submit
        if (!this.validateCurrentStep()) {
            e.preventDefault();
            return;
        }

        // Generate cropped avatar before submit
        this.generateCroppedAvatar();

        // Populate avatar focus data (normalized pan/zoom for display)
        if (typeof this._getAvatarFocusData === 'function') {
            const focusData = this._getAvatarFocusData();
            const zoomInput = document.getElementById('avatar_zoom_input');
            const panXInput = document.getElementById('avatar_pan_x_input');
            const panYInput = document.getElementById('avatar_pan_y_input');
            
            if (zoomInput) zoomInput.value = focusData.zoom || 1;
            if (panXInput) panXInput.value = focusData.panXNorm || 0;
            if (panYInput) panYInput.value = focusData.panYNorm || 0;
        }

        // Show loading state
        if (this.submitBtn) {
            this.submitBtn.disabled = true;
            const textEl = this.submitBtn.querySelector(".submit-text");
            const loadingEl = this.submitBtn.querySelector(".submit-loading");

            if (textEl) textEl.classList.add("hidden");
            if (loadingEl) loadingEl.classList.remove("hidden");
        }
    }

    generateCroppedAvatar() {
        // This calls the real implementation stored during initProfilePicture
        if (typeof this._generateCroppedAvatarImpl === 'function') {
            this._generateCroppedAvatarImpl();
        }
    }

    nextStep() {
        if (this.validateCurrentStep()) {
            this.completedSteps.add(this.currentStep); // Mark as completed
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.updateUI();
                this.updateProgress();
            }
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateUI();
            this.updateProgress();
        }
    }

    updateUI() {
        // Guard
        if (!this.form) return;

        // Hide all steps
        document.querySelectorAll(".onboarding-step").forEach((step) => {
            step.classList.add("hidden");
            step.classList.remove("active");
        });

        // Show current step
        const currentStepEl = document.getElementById(
            `step-${this.currentStep}`
        );
        if (currentStepEl) {
            currentStepEl.classList.remove("hidden");
            currentStepEl.classList.add("active");
        }

        // Update navigation buttons
        if (this.prevBtn)
            this.prevBtn.classList.toggle("hidden", this.currentStep === 1);
        if (this.nextBtn)
            this.nextBtn.classList.toggle(
                "hidden",
                this.currentStep === this.totalSteps
            );
        if (this.submitBtn)
            this.submitBtn.classList.toggle(
                "hidden",
                this.currentStep !== this.totalSteps
            );
    }

    updateProgress() {
        // Guard
        if (!this.form) return;
        // Update step indicators with 3 states: active (blue), done (green), inactive (gray)
        document.querySelectorAll(".step-indicator").forEach((indicator) => {
            const stepNum = parseInt(indicator.dataset.step);

            // Reset classes
            indicator.classList.remove(
                "bg-blue-600",
                "bg-green-600",
                "bg-slate-200",
                "text-white",
                "text-slate-500"
            );

            if (stepNum === this.currentStep) {
                // ACTIVE - blue
                indicator.classList.add("bg-blue-600", "text-white");
            } else if (this.completedSteps.has(stepNum)) {
                // DONE - green
                indicator.classList.add("bg-green-600", "text-white");
            } else {
                // INACTIVE - gray
                indicator.classList.add("bg-slate-200", "text-slate-500");
            }
        });

        // Update step labels
        document.querySelectorAll(".step-label").forEach((label) => {
            const stepNum = parseInt(label.dataset.step);

            // Reset classes
            label.classList.remove(
                "text-blue-600",
                "text-green-600",
                "text-slate-500"
            );

            if (stepNum === this.currentStep) {
                label.classList.add("text-blue-600");
            } else if (this.completedSteps.has(stepNum)) {
                label.classList.add("text-green-600");
            } else {
                label.classList.add("text-slate-500");
            }
        });

        // Progress lines tetap abu-abu (tidak berubah warna)
    }

    validateCurrentStep() {
        let isValid = true;

        if (this.currentStep === 1) {
            // Validate personal info
            const firstName = document.getElementById("first_name");
            const lastName = document.getElementById("last_name");
            const birthDate = document.getElementById("birth_date");
            const dateDisplay = document.getElementById("date-display");

            if (!firstName.value.trim()) {
                this.showFieldError(firstName, "Nama depan harus diisi");
                isValid = false;
            } else {
                this.clearFieldError(firstName);
            }

            if (!lastName.value.trim()) {
                this.showFieldError(lastName, "Nama belakang harus diisi");
                isValid = false;
            } else {
                this.clearFieldError(lastName);
            }

            // Check both hidden date input and display input
            if (!birthDate.value && !dateDisplay.value.trim()) {
                const errorEl = document.getElementById("birth-date-error");
                if (errorEl) {
                    errorEl.textContent = "Tanggal lahir masih kosong.";
                    errorEl.classList.remove("hidden");
                }
                dateDisplay.classList.add("border-red-500");
                dateDisplay.classList.remove("border-slate-300");
                isValid = false;
            } else {
                const errorEl = document.getElementById("birth-date-error");
                if (errorEl) {
                    errorEl.classList.add("hidden");
                }
                dateDisplay.classList.remove("border-red-500");
                dateDisplay.classList.add("border-slate-300");
            }
        }

        if (this.currentStep === 2) {
            // Validate role selection
            const roleInputs = document.querySelectorAll(
                'input[name="preferred_role"]'
            );
            const isRoleSelected = Array.from(roleInputs).some(
                (input) => input.checked
            );

            if (!isRoleSelected) {
                this.showError("Pilih role yang diinginkan");
                isValid = false;
            }
        }

        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add("border-red-500");

        // Remove existing error
        const existingError = field.parentElement.querySelector(".field-error");
        if (existingError) {
            existingError.remove();
        }

        // Add new error
        const errorEl = document.createElement("p");
        errorEl.className = "field-error text-red-600 text-sm mt-1";
        errorEl.textContent = message;
        field.parentElement.appendChild(errorEl);
    }

    clearFieldError(field) {
        field.classList.remove("border-red-500");
        const errorEl = field.parentElement.querySelector(".field-error");
        if (errorEl) {
            errorEl.remove();
        }
    }

    showError(message) {
        // Simple alert for now - could be improved with toast notifications
        alert(message);
    }

    initMiddleNameToggle() {
        const toggle = document.getElementById("show-middle-name");
        const field = document.getElementById("middle-name-field");
        const input = document.getElementById("middle_name");

        if (toggle && field && input) {
            toggle.addEventListener("change", () => {
                if (toggle.checked) {
                    field.classList.remove("hidden");
                    setTimeout(() => input.focus(), 300);
                } else {
                    field.classList.add("hidden");
                    input.value = "";
                }
            });
        }
    }

    initProfilePicture() {
        const input = document.getElementById("profile_picture");
        const dropzone = document.getElementById("avatar-dropzone");
        const imgContainer = document.getElementById("avatar-img-container");
        const img = document.getElementById("avatar-img");
        const placeholder = document.getElementById("avatar-placeholder");
        const dropOverlay = document.getElementById("avatar-drop-overlay");
        const editOverlay = document.getElementById("avatar-edit-overlay");
        const circleMask = document.getElementById("avatar-circle-mask"); // Circular mask overlay
        const actions = document.getElementById("avatar-actions");
        const deleteBtn = document.getElementById("avatar-delete");
        const resetBtn = document.getElementById("avatar-reset");
        const changeBtn = document.getElementById("avatar-change");
        const zoomContainer = document.getElementById("avatar-zoom-container");
        const zoomSlider = document.getElementById("avatar-zoom");
        const errorEl = document.getElementById("avatar-error");
        const hintEl = document.getElementById("avatar-hint");

        if (!input || !dropzone || !img || !placeholder) return;

        const MAX_SIZE = 5 * 1024 * 1024;
        const ALLOWED_TYPES = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
        ];
        // HORIZONTAL COVER - use real DOM size (responsive)
        const CIRCLE_RADIUS = 80; // Circle for navbar avatar
        const CIRCLE_DIAMETER = CIRCLE_RADIUS * 2; // 160px
        const DRAG_THRESHOLD = 5; // Minimum px movement to start drag

        /**
         * Get REAL frame size from DOM (responsive)
         */
        const getFrameSize = () => {
            const rect = dropzone.getBoundingClientRect();
            return { frameW: rect.width, frameH: rect.height };
        };

        // State
        let hasImage = false;
        let isEditing = false; // Edit mode flag - blocks upload when true
        
        // Scale state - CENTER-BASED positioning
        let baseCoverScale = 1; // Computed once per image to make it cover the frame
        let userZoom = 1; // User-controlled zoom (1.0 = default)
        
        // Pan state - CENTER-BASED (0,0 = centered)
        let panX = 0;
        let panY = 0;
        
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let mouseDownX = 0;
        let mouseDownY = 0;
        let imgNaturalW = 0;
        let imgNaturalH = 0;
        let currentObjectURL = null; // Track for cleanup

        const showError = (msg) => {
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove("hidden");
            }
        };

        const hideError = () => errorEl?.classList.add("hidden");

        /**
         * CENTER-BASED transform positioning
         * panX=0, panY=0 means IMAGE IS CENTERED
         */
        const updateImageTransform = (animate = false) => {
            if (!img || !imgNaturalW || !imgNaturalH || !baseCoverScale) return;

            // Get REAL frame size from DOM
            const { frameW, frameH } = getFrameSize();

            // Final scale = base cover scale * user zoom
            const finalScale = baseCoverScale * userZoom;

            // Calculate scaled dimensions
            const scaledW = imgNaturalW * finalScale;
            const scaledH = imgNaturalH * finalScale;

            // Calculate excess area
            const excessW = scaledW - frameW;
            const excessH = scaledH - frameH;

            // Calculate max pan with soft bounds
            let maxPanX = Math.max(0, excessW / 2);
            let maxPanY = Math.max(0, excessH / 2);

            // SOFT BOUNDS: allow panning even when zoomed out
            const softBoundX = frameW * 0.35;
            const softBoundY = frameH * 0.35;
            maxPanX = Math.max(maxPanX, softBoundX);
            maxPanY = Math.max(maxPanY, softBoundY);

            // Clamp pan values symmetrically around center
            panX = Math.max(-maxPanX, Math.min(maxPanX, panX));
            panY = Math.max(-maxPanY, Math.min(maxPanY, panY));

            // Apply CENTER-BASED transform
            img.style.transition = animate ? "all 0.25s ease-out" : "none";
            img.style.position = "absolute";
            img.style.left = "50%";
            img.style.top = "50%";
            img.style.width = `${imgNaturalW}px`;
            img.style.height = `${imgNaturalH}px`;
            img.style.maxWidth = "none";
            img.style.maxHeight = "none";
            img.style.objectFit = "none";
            img.style.transformOrigin = "center";
            img.style.transform = `translate(-50%, -50%) translate(${panX}px, ${panY}px) scale(${finalScale})`;
        };

        const centerImage = (animate = true) => {
            if (!imgNaturalW || !imgNaturalH) return;
            panX = 0;
            panY = 0;
            updateImageTransform(animate);
        };

        /**
         * Generate cropped avatar from the circle area.
         * Uses CENTER-BASED coordinates to correctly extract the visible circle.
         */
        const generateCroppedAvatar = () => {
            const canvas = document.getElementById("avatar-canvas");
            const croppedInput = document.getElementById("cropped_avatar");

            if (!canvas || !croppedInput || !img || !img.src || img.src === "") {
                if (croppedInput) croppedInput.value = "";
                return;
            }

            const ctx = canvas.getContext("2d");
            const OUTPUT_SIZE = 256; // Output canvas size

            // Get frame size (responsive)
            const { frameW, frameH } = getFrameSize();

            // Final scale = baseCoverScale * userZoom
            const finalScale = baseCoverScale * userZoom;

            // Scaled image dimensions
            const scaledW = imgNaturalW * finalScale;
            const scaledH = imgNaturalH * finalScale;

            // Frame center
            const frameCenterX = frameW / 2;
            const frameCenterY = frameH / 2;

            // Image center in frame coordinates (with pan offset)
            const imgCenterX = frameCenterX + panX;
            const imgCenterY = frameCenterY + panY;

            // Image top-left corner in frame coordinates
            const imgLeft = imgCenterX - scaledW / 2;
            const imgTop = imgCenterY - scaledH / 2;

            // Circle area to crop (center of frame)
            const circleLeft = frameCenterX - CIRCLE_RADIUS;
            const circleTop = frameCenterY - CIRCLE_RADIUS;

            // Calculate source rectangle in original image coordinates
            const srcX = (circleLeft - imgLeft) / finalScale;
            const srcY = (circleTop - imgTop) / finalScale;
            const srcW = CIRCLE_DIAMETER / finalScale;
            const srcH = CIRCLE_DIAMETER / finalScale;

            // Clear and draw
            ctx.clearRect(0, 0, OUTPUT_SIZE, OUTPUT_SIZE);

            try {
                ctx.drawImage(
                    img,
                    srcX, srcY, srcW, srcH,
                    0, 0, OUTPUT_SIZE, OUTPUT_SIZE
                );

                const dataURL = canvas.toDataURL("image/jpeg", 0.9);
                croppedInput.value = dataURL;
            } catch (err) {
                console.error("Failed to generate cropped avatar:", err);
                croppedInput.value = "";
            }
        };

        // Store reference on class instance for handleSubmit to access
        this._generateCroppedAvatarImpl = generateCroppedAvatar;

        // Return normalized pan/zoom for avatar_focus storage
        const getAvatarFocusData = () => {
            const { frameW, frameH } = getFrameSize();
            return {
                zoom: userZoom,
                panXNorm: frameW > 0 ? panX / frameW : 0,
                panYNorm: frameH > 0 ? panY / frameH : 0,
            };
        };
        this._getAvatarFocusData = getAvatarFocusData;

        const showImageUI = () => {
            hasImage = true;
            imgContainer?.classList.remove("hidden");
            circleMask?.classList.remove("hidden"); // Show circular mask
            placeholder.classList.add("hidden");
            actions?.classList.remove("opacity-0");
            actions?.classList.add("opacity-100");
            zoomContainer?.classList.remove("hidden");
            dropzone.style.cursor = "grab"; // Change to grab when has image
            if (hintEl)
                hintEl.textContent = "Geser fotonya biar pas di lingkaran tengah";
        };

        const hideImageUI = () => {
            hasImage = false;
            isEditing = false;
            imgContainer?.classList.add("hidden");
            circleMask?.classList.add("hidden"); // Hide circular mask
            placeholder.classList.remove("hidden");
            actions?.classList.add("opacity-0");
            actions?.classList.remove("opacity-100");
            zoomContainer?.classList.add("hidden");
            dropzone.style.cursor = "pointer"; // Change back to pointer
            if (hintEl)
                hintEl.textContent =
                    "Ini opsional kok, tapi bikin profilmu lebih hidup";
        };

        const loadImage = (file) => {
            hideError();

            if (!ALLOWED_TYPES.includes(file.type)) {
                showError(
                    "Waduh, format-nya gak cocok. Coba JPG, PNG, atau GIF ya!"
                );
                return;
            }

            if (file.size > MAX_SIZE) {
                showError("Filenya kegedean nih, max 5MB aja ya!");
                return;
            }

            // IMPORTANT: Revoke old URL to prevent memory leak
            if (currentObjectURL) {
                URL.revokeObjectURL(currentObjectURL);
                currentObjectURL = null;
            }

            // Reset all state for new image
            baseCoverScale = 1;
            userZoom = 1;
            panX = 0;
            panY = 0;
            imgNaturalW = 0;
            imgNaturalH = 0;
            isDragging = false;
            isEditing = false;
            if (zoomSlider) zoomSlider.value = 100;

            // Create fresh URL
            currentObjectURL = URL.createObjectURL(file);

            // Force image reload by clearing src first
            img.src = "";

            img.onload = () => {
                imgNaturalW = img.naturalWidth;
                imgNaturalH = img.naturalHeight;

                // Get REAL frame size from DOM
                const { frameW, frameH } = getFrameSize();

                // Compute baseCoverScale - image fills frame
                baseCoverScale = Math.max(
                    frameW / imgNaturalW,
                    frameH / imgNaturalH
                );

                // Reset pan to center
                panX = 0;
                panY = 0;

                // Apply initial transform using center-based positioning
                const finalScale = baseCoverScale * userZoom;
                img.style.position = "absolute";
                img.style.left = "50%";
                img.style.top = "50%";
                img.style.transformOrigin = "center";
                img.style.width = `${imgNaturalW}px`;
                img.style.height = `${imgNaturalH}px`;
                img.style.maxWidth = "none";
                img.style.maxHeight = "none";
                img.style.objectFit = "none";
                img.style.transform = `translate(-50%, -50%) translate(0px, 0px) scale(${finalScale})`;
                img.style.transition = "none";

                showImageUI();

                // Entry animation: fade in
                img.style.opacity = "0";
                requestAnimationFrame(() => {
                    img.style.transition = "opacity 0.3s ease";
                    img.style.opacity = "1";
                    // Generate initial cropped avatar after load
                    generateCroppedAvatar();
                });
            };

            img.src = currentObjectURL;
        };

        const reset = () => {
            // Revoke URL
            if (currentObjectURL) {
                URL.revokeObjectURL(currentObjectURL);
                currentObjectURL = null;
            }

            input.value = "";
            img.src = "";
            baseCoverScale = 1;
            userZoom = 1;
            panX = 0;
            panY = 0;
            imgNaturalW = 0;
            imgNaturalH = 0;
            isDragging = false;
            isEditing = false;
            if (zoomSlider) zoomSlider.value = 100;
            // Clear cropped avatar when image is removed
            const croppedInput = document.getElementById("cropped_avatar");
            if (croppedInput) croppedInput.value = "";
            hideImageUI();
            hideError();
        };

        // === CLICK TO UPLOAD (ONLY when no image) ===
        // When image exists, click on circle = start drag, NOT upload
        dropzone.addEventListener("click", (e) => {
            // NEVER trigger upload if has image - use "Ganti foto" button instead
            if (hasImage) return;

            // Block if currently dragging
            if (isDragging || isEditing) return;

            input.click();
        });

        // === CHANGE PHOTO BUTTON (explicit upload trigger) ===
        changeBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            input.click();
        });

        // === FILE INPUT CHANGE ===
        input.addEventListener("change", () => {
            const file = input.files?.[0];
            if (file) loadImage(file);
        });

        // === DRAG & DROP FROM OUTSIDE (file upload) ===
        let isDroppingFile = false;

        dropzone.addEventListener("dragenter", (e) => {
            // Only show drop UI if dragging files from outside
            if (!e.dataTransfer?.types?.includes("Files")) return;
            e.preventDefault();
            isDroppingFile = true;
            dropOverlay?.classList.remove("opacity-0");
            dropOverlay?.classList.add("opacity-100");
            dropzone.classList.add("border-blue-500", "scale-105");
        });

        dropzone.addEventListener("dragover", (e) => {
            if (!e.dataTransfer?.types?.includes("Files")) return;
            e.preventDefault();
        });

        dropzone.addEventListener("dragleave", (e) => {
            if (!isDroppingFile) return;
            e.preventDefault();

            // Check if actually leaving dropzone
            const rect = dropzone.getBoundingClientRect();
            if (
                e.clientX >= rect.left &&
                e.clientX <= rect.right &&
                e.clientY >= rect.top &&
                e.clientY <= rect.bottom
            )
                return;

            isDroppingFile = false;
            dropOverlay?.classList.add("opacity-0");
            dropOverlay?.classList.remove("opacity-100");
            dropzone.classList.remove("border-blue-500", "scale-105");
        });

        dropzone.addEventListener("drop", (e) => {
            e.preventDefault();
            e.stopPropagation();

            isDroppingFile = false;
            dropOverlay?.classList.add("opacity-0");
            dropOverlay?.classList.remove("opacity-100");
            dropzone.classList.remove("border-blue-500", "scale-105");

            // Only process if has files
            const file = e.dataTransfer?.files?.[0];
            if (file && file.type.startsWith("image/")) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                loadImage(file);
            }
        });

        // === PAN/DRAG IMAGE (reposition) ===
        const startDrag = (clientX, clientY) => {
            if (!hasImage) return;

            mouseDownX = clientX;
            mouseDownY = clientY;
            dragStartX = clientX - panX;
            dragStartY = clientY - panY;

            // Don't set isDragging yet - wait for threshold
            isEditing = true;
        };

        const moveDrag = (clientX, clientY) => {
            if (!hasImage || !isEditing) return;

            // Check threshold before starting actual drag
            if (!isDragging) {
                const dx = Math.abs(clientX - mouseDownX);
                const dy = Math.abs(clientY - mouseDownY);
                if (dx < DRAG_THRESHOLD && dy < DRAG_THRESHOLD) return;

                // Threshold passed - start dragging
                isDragging = true;
                dropzone.style.cursor = "grabbing";
                editOverlay?.classList.remove("opacity-0");
                guide?.classList.remove("hidden");
            }

            panX = clientX - dragStartX;
            panY = clientY - dragStartY;
            updateImageTransform(false);
        };

        const endDrag = () => {
            const wasDragging = isDragging;
            isDragging = false;
            isEditing = false;

            dropzone.style.cursor = hasImage ? "grab" : "pointer";
            editOverlay?.classList.add("opacity-0");
            guide?.classList.add("hidden");

            if (wasDragging) {
                // Smooth snap to bounds
                updateImageTransform(true);
                // Update cropped avatar after pan
                generateCroppedAvatar();
            }
        };

        // Mouse events for pan
        dropzone.addEventListener("mousedown", (e) => {
            if (!hasImage) return;

            // CRITICAL: Stop event from bubbling to click handler
            e.preventDefault();
            e.stopPropagation();

            startDrag(e.clientX, e.clientY);
        });

        document.addEventListener("mousemove", (e) => {
            if (isEditing) moveDrag(e.clientX, e.clientY);
        });

        document.addEventListener("mouseup", () => {
            if (isEditing) endDrag();
        });

        // Touch events for pan
        dropzone.addEventListener(
            "touchstart",
            (e) => {
                if (!hasImage || e.touches.length !== 1) return;

                // CRITICAL: Stop event from triggering click/upload
                e.preventDefault();
                e.stopPropagation();

                const touch = e.touches[0];
                startDrag(touch.clientX, touch.clientY);
            },
            { passive: false }
        );

        dropzone.addEventListener(
            "touchmove",
            (e) => {
                if (!isEditing || e.touches.length !== 1) return;
                e.preventDefault();
                const touch = e.touches[0];
                moveDrag(touch.clientX, touch.clientY);
            },
            { passive: false }
        );

        dropzone.addEventListener("touchend", () => {
            if (isEditing) endDrag();
        });

        // === ZOOM SLIDER ===
        zoomSlider?.addEventListener("input", () => {
            // userZoom maps slider value (60-250) to (0.6x - 2.5x)
            userZoom = Number(zoomSlider.value) / 100;
            updateImageTransform(false);
            // Update cropped avatar after zoom
            generateCroppedAvatar();
        });

        // === RESET BUTTON ===
        resetBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            userZoom = 1;
            panX = 0;
            panY = 0;
            if (zoomSlider) zoomSlider.value = 100;
            updateImageTransform(true);
            // Update cropped avatar after reset
            generateCroppedAvatar();
        });

        // === DELETE BUTTON ===
        deleteBtn?.addEventListener("click", (e) => {
            e.stopPropagation();

            // Animate out
            if (imgContainer) {
                imgContainer.style.transition =
                    "transform 0.2s ease, opacity 0.2s ease";
                imgContainer.style.transform = "scale(0.8)";
                imgContainer.style.opacity = "0";
                setTimeout(() => {
                    reset();
                    if (imgContainer) {
                        imgContainer.style.transform = "";
                        imgContainer.style.opacity = "";
                    }
                }, 200);
            } else {
                reset();
            }
        });

        // Set initial cursor
        dropzone.style.cursor = "pointer";
    }

    initAgeCalculation() {
        const birthDateInput = document.getElementById("birth_date");
        const dateDisplay = document.getElementById("date-display");
        const ageDisplay = document.getElementById("age-display");
        const ageText = document.getElementById("age-text");
        const errorEl = document.getElementById("birth-date-error");

        if (!birthDateInput || !ageDisplay || !ageText) return;

        let isTouched = false;

        const calcAge = (iso) => {
            const parts = iso.split("-");
            if (parts.length !== 3) return null;

            const y = Number(parts[0]);
            const m = Number(parts[1]);
            const d = Number(parts[2]);
            if (!y || !m || !d) return null;

            const today = new Date();
            let age = today.getFullYear() - y;

            const hadBirthday =
                today.getMonth() + 1 > m ||
                (today.getMonth() + 1 === m && today.getDate() >= d);

            if (!hadBirthday) age -= 1;
            return age;
        };

        const showError = (msg) => {
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove("hidden");
            }
            if (dateDisplay) {
                dateDisplay.classList.add("border-red-500");
                dateDisplay.classList.remove("border-slate-300");
            }
            ageDisplay.classList.add("hidden");
        };

        const clearError = () => {
            if (errorEl) {
                errorEl.classList.add("hidden");
            }
            if (dateDisplay) {
                dateDisplay.classList.remove("border-red-500");
                dateDisplay.classList.add("border-slate-300");
            }
        };

        const render = () => {
            const iso = (birthDateInput.value || "").trim();

            if (!iso) {
                ageText.textContent = "-";
                ageDisplay.classList.add("hidden");
                if (isTouched) {
                    showError("Tanggal lahir masih kosong.");
                }
                return;
            }

            clearError();

            const age = calcAge(iso);
            if (age === null || age < 0 || age > 120) {
                ageText.textContent = "-";
                ageDisplay.classList.add("hidden");
                return;
            }

            ageText.textContent = String(age);
            ageDisplay.classList.remove("hidden");

            if (!ageDisplay.textContent.includes("Umur:")) {
                ageDisplay.innerHTML = `Umur: <span id="age-text">${age}</span> tahun`;
            }
        };

        if (dateDisplay) {
            dateDisplay.addEventListener("focus", () => {
                isTouched = true;
            });

            dateDisplay.addEventListener("blur", () => {
                if (!isTouched) return;
                render();
            });
        }

        birthDateInput.addEventListener("change", () => {
            isTouched = true;
            render();
        });

        if (birthDateInput.value) {
            render();
        }
    }

    initBirthDateModal() {
        const openBtn = document.getElementById("open-date-modal");
        const modal = document.getElementById("date-modal");
        const closeBtn = document.getElementById("close-modal");
        const cancelBtn = document.getElementById("cancel-modal");
        const confirmBtn = document.getElementById("confirm-modal");

        const yearSelect = document.getElementById("year-select");
        const monthSelect = document.getElementById("month-select");
        const daySelect = document.getElementById("day-select");

        const birthDateInput = document.getElementById("birth_date");
        const dateDisplay = document.getElementById("date-display");

        // Guard: cuma jalan kalau elemen ada
        if (
            !openBtn ||
            !modal ||
            !yearSelect ||
            !monthSelect ||
            !daySelect ||
            !birthDateInput ||
            !dateDisplay
        )
            return;

        const pad2 = (n) => String(n).padStart(2, "0");
        const formatDisplay = (yyyy, mm, dd) =>
            `${pad2(dd)}/${pad2(mm)}/${yyyy}`;

        const setHiddenDate = (yyyy, mm, dd) => {
            birthDateInput.value = `${yyyy}-${pad2(mm)}-${pad2(dd)}`;
            birthDateInput.dispatchEvent(
                new Event("change", { bubbles: true })
            );
            dateDisplay.value = `${pad2(dd)}/${pad2(mm)}/${yyyy}`;
        };

        const buildYears = () => {
            const now = new Date();
            const currentYear = now.getFullYear();
            const minYear = currentYear - 100;

            yearSelect.innerHTML = '<option value="">--</option>';
            for (let y = currentYear; y >= minYear; y--) {
                const opt = document.createElement("option");
                opt.value = String(y);
                opt.textContent = String(y);
                yearSelect.appendChild(opt);
            }
        };

        const daysInMonth = (year, month) =>
            new Date(Number(year), Number(month), 0).getDate();

        const buildDays = () => {
            const y = yearSelect.value;
            const m = monthSelect.value;

            // Jika tahun atau bulan belum dipilih, tampilkan placeholder saja
            if (!y || !m) {
                daySelect.innerHTML = '<option value="">--</option>';
                return;
            }

            const max = daysInMonth(y, m);
            const current = Number(daySelect.value || 0);

            daySelect.innerHTML = '<option value="">--</option>';
            for (let d = 1; d <= max; d++) {
                const opt = document.createElement("option");
                opt.value = pad2(d);
                opt.textContent = pad2(d);
                daySelect.appendChild(opt);
            }

            if (current >= 1 && current <= max) {
                daySelect.value = pad2(current);
            }
        };

        const open = () => {
            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden");
        };

        const close = () => {
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
        };

        const syncFromHidden = () => {
            if (birthDateInput.value) {
                const [yyyy, mm, dd] = birthDateInput.value.split("-");
                if (yyyy && mm && dd) {
                    yearSelect.value = yyyy;
                    monthSelect.value = mm;
                    buildDays();
                    daySelect.value = dd;
                    dateDisplay.value = `${dd}/${mm}/${yyyy}`;
                    return;
                }
            }

            // Kosongkan semua - tidak ada default
            dateDisplay.value = "";
            birthDateInput.value = "";
            yearSelect.value = "";
            monthSelect.value = "";
            buildDays(); // akan tampilkan placeholder karena year/month kosong
        };

        // Init dropdown
        buildYears();
        buildDays();
        syncFromHidden();

        openBtn.addEventListener("click", () => {
            syncFromHidden();
            open();
        });

        [closeBtn, cancelBtn]
            .filter(Boolean)
            .forEach((btn) => btn.addEventListener("click", close));

        // klik backdrop buat close
        modal.addEventListener("click", (e) => {
            if (e.target === modal) close();
        });

        // ESC buat close
        document.addEventListener("keydown", (e) => {
            if (!modal.classList.contains("hidden") && e.key === "Escape")
                close();
        });

        yearSelect.addEventListener("change", buildDays);
        monthSelect.addEventListener("change", buildDays);

        if (confirmBtn) {
            confirmBtn.addEventListener("click", () => {
                const yyyy = yearSelect.value;
                const mm = monthSelect.value;
                const dd = daySelect.value;

                // Validasi: semua harus dipilih
                if (!yyyy || !mm || !dd) {
                    return; // Jangan simpan jika belum lengkap
                }

                setHiddenDate(yyyy, Number(mm), Number(dd));
                close();
            });
        }

        // Manual typing DD/MM/YYYY
        dateDisplay.addEventListener("blur", () => {
            const raw = dateDisplay.value.trim();
            if (!raw) {
                birthDateInput.value = "";
                birthDateInput.dispatchEvent(
                    new Event("change", { bubbles: true })
                );
                return;
            }

            let dd, mm, yyyy;

            // YYYY-MM-DD
            let m1 = raw.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
            if (m1) {
                yyyy = m1[1];
                mm = Number(m1[2]);
                dd = Number(m1[3]);
            } else {
                // DD/MM/YYYY atau DD-MM-YYYY
                let m2 = raw.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
                if (!m2) return;

                dd = Number(m2[1]);
                mm = Number(m2[2]);
                yyyy = m2[3];
            }

            const max = new Date(Number(yyyy), Number(mm), 0).getDate();
            if (mm < 1 || mm > 12 || dd < 1 || dd > max) return;

            const pad2 = (n) => String(n).padStart(2, "0");

            // Sync dropdown modal
            yearSelect.value = String(yyyy);
            monthSelect.value = pad2(mm);
            buildDays();
            daySelect.value = pad2(dd);

            // ? hidden input (DB format)
            birthDateInput.value = `${yyyy}-${pad2(mm)}-${pad2(dd)}`;
            birthDateInput.dispatchEvent(
                new Event("change", { bubbles: true })
            );

            // ? UI format konsisten
            dateDisplay.value = `${pad2(dd)}/${pad2(mm)}/${yyyy}`;
        });
    }

    initFocusAreas() {
        const inputs = document.querySelectorAll('input[name="focus_areas[]"]');
        const countEl = document.getElementById("selected-focus-count");

        if (!inputs.length) return;

        const applyUI = (input) => {
            const card = input.closest(".focus-area-card");
            const content = card?.querySelector(".focus-area-content");
            const badge = card?.querySelector(".selected-badge");
            if (!content) return;

            if (input.checked) {
                content.classList.add(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                content.classList.remove("border-slate-200");
                if (badge) badge.classList.remove("hidden");
            } else {
                content.classList.remove(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                content.classList.add("border-slate-200");
                if (badge) badge.classList.add("hidden");
            }
        };

        const sync = () => {
            const checked = Array.from(inputs)
                .filter((i) => i.checked)
                .map((i) => i.value);

            if (countEl) countEl.textContent = String(checked.length);

            // refresh UI
            inputs.forEach(applyUI);
        };

        inputs.forEach((input) => {
            input.addEventListener("change", sync);
        });

        // initial load (kalau ada old() yang checked)
        sync();
    }

    initRoleSelection() {
        const roleInputs = document.querySelectorAll(
            'input[name="preferred_role"]'
        );
        const mentorNotice = document.getElementById("mentor-notice");

        if (!roleInputs.length) return;

        const getCard = (input) => {
            return (
                input.closest(".role-card") ||
                input.closest("label") ||
                input.closest("div")
            );
        };

        const applyUI = () => {
            roleInputs.forEach((input) => {
                const card = getCard(input);
                if (!card) return;

                if (input.checked) {
                    card.classList.add(
                        "ring-2",
                        "ring-blue-500",
                        "border-blue-400",
                        "bg-blue-50/60"
                    );
                    card.classList.remove("border-slate-200");
                } else {
                    card.classList.remove(
                        "ring-2",
                        "ring-blue-500",
                        "border-blue-400",
                        "bg-blue-50/60"
                    );
                    card.classList.add("border-slate-200");
                }
            });
        };

        const syncMentorNotice = () => {
            if (!mentorNotice) return;
            const selected = Array.from(roleInputs).find((i) => i.checked);
            if (selected?.value === "mentor")
                mentorNotice.classList.remove("hidden");
            else mentorNotice.classList.add("hidden");
        };

        roleInputs.forEach((input) => {
            input.addEventListener("change", () => {
                applyUI();
                syncMentorNotice();
            });
        });

        // Initial
        applyUI();
        syncMentorNotice();
    }
    initFocusAreasUI() {
        const cards = document.querySelectorAll("[data-focus]");
        const selectedText = document.getElementById("selected-focus-count");
        const hiddenInput = document.getElementById("focus_areas");

        if (!cards.length) return;

        const selected = new Set();

        const applyUI = (card, isOn) => {
            const badge = card.querySelector(".selected-badge");

            if (isOn) {
                card.classList.add(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                card.classList.remove("border-slate-200");
                if (badge) badge.classList.remove("hidden");
            } else {
                card.classList.remove(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                card.classList.add("border-slate-200");
                if (badge) badge.classList.add("hidden");
            }
        };

        const sync = () => {
            const arr = Array.from(selected);
            if (selectedText)
                selectedText.textContent = `${arr.length} area dipilih`;
            if (hiddenInput) hiddenInput.value = JSON.stringify(arr);
        };

        cards.forEach((card) => {
            applyUI(card, false);

            card.addEventListener("click", () => {
                const val = card.getAttribute("data-focus");
                if (!val) return;

                if (selected.has(val)) {
                    selected.delete(val);
                    applyUI(card, false);
                } else {
                    selected.add(val);
                    applyUI(card, true);
                }
                sync();
            });
        });

        sync();
    }
    initRoleSelectUI() {
        const cards = document.querySelectorAll("[data-role]");
        const hidden = document.getElementById("role");

        if (!cards.length || !hidden) return;

        const apply = (card, on) => {
            const badge = card.querySelector(".role-selected-badge");

            if (on) {
                card.classList.add(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                card.classList.remove("border-slate-200");
                if (badge) badge.classList.remove("hidden");
            } else {
                card.classList.remove(
                    "ring-2",
                    "ring-blue-500",
                    "border-blue-400",
                    "bg-blue-50/60"
                );
                card.classList.add("border-slate-200");
                if (badge) badge.classList.add("hidden");
            }
        };

        const clearAll = () => cards.forEach((c) => apply(c, false));

        cards.forEach((card) => {
            apply(card, false);

            card.addEventListener("click", () => {
                const role = card.getAttribute("data-role");
                if (!role) return;

                clearAll();
                apply(card, true);
                hidden.value = role;
            });
        });
    }

    initValidation() {
        // Real-time validation
        const inputs = document.querySelectorAll("input[required]");
        inputs.forEach((input) => {
            input.addEventListener("blur", () => {
                if (input.value.trim()) {
                    this.clearFieldError(input);
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new OnboardingManager();
});

// Export for potential use in other modules
export default OnboardingManager;
