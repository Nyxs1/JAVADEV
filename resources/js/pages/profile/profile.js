/**
 * Profile Page JavaScript
 * Handles profile editing, avatar upload with crop/pan/zoom, and form interactions
 */

class ProfileManager {
    constructor() {
        // Only init if we're on profile page
        if (!document.getElementById("profile-details")) return;

        this.editBtn = document.getElementById("edit-profile-btn");
        this.editSection = document.getElementById("edit-form-section");
        this.cancelBtn = document.getElementById("cancel-edit");
        this.profileDetails = document.getElementById("profile-details");
        this.bioInput = document.getElementById("bio");
        this.bioCount = document.getElementById("bio-count");
        this.middleNameToggle = document.getElementById("show-middle-name");
        this.middleNameField = document.getElementById("middle-name-field");
        this.profileForm = document.getElementById("profile-form");

        this.init();
    }

    init() {
        this.initEditToggle();
        this.initProfilePicture();
        this.initBioCounter();
        this.initMiddleNameToggle();
        this.initFormSubmit();
    }

    initEditToggle() {
        if (this.editBtn && this.editSection) {
            this.editBtn.addEventListener("click", () => {
                this.editSection.classList.remove("hidden");
                this.profileDetails?.classList.add("hidden");
                this.editBtn.classList.add("hidden");
            });
        }

        if (this.cancelBtn) {
            this.cancelBtn.addEventListener("click", () => {
                this.editSection?.classList.add("hidden");
                this.profileDetails?.classList.remove("hidden");
                this.editBtn?.classList.remove("hidden");
            });
        }
    }

    initProfilePicture() {
        const input = document.getElementById("profile_picture");
        const dropzone = document.getElementById("avatar-dropzone");
        const imgContainer = document.getElementById("avatar-img-container");
        const img = document.getElementById("avatar-img");
        const blurImg = document.getElementById("avatar-blur-img"); // NEW: Blurred background image
        const blurContainer = document.getElementById("avatar-blur-bg-container");
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
        const croppedInput = document.getElementById("cropped_avatar");
        const removeAvatarInput = document.getElementById("remove_avatar");

        if (!input || !dropzone || !img || !placeholder) return;

        const MAX_SIZE = 5 * 1024 * 1024;
        const ALLOWED_TYPES = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/gif",
        ];
        // HORIZONTAL COVER - use real DOM size (responsive)
        // Removed hardcoded dimensions - now computed dynamically
        const CIRCLE_RADIUS = 80; // Circle for navbar avatar
        const CIRCLE_DIAMETER = CIRCLE_RADIUS * 2; // 160px
        const DRAG_THRESHOLD = 5;

        /**
         * Get REAL frame size from DOM (responsive)
         * This is the single source of truth for frame dimensions
         */
        const getFrameSize = () => {
            const rect = dropzone.getBoundingClientRect();
            return { frameW: rect.width, frameH: rect.height };
        };

        // State - single source of truth
        let hasImage =
            imgContainer && !imgContainer.classList.contains("hidden");
        let isEditing = false;

        // Scale state
        let baseCoverScale = 1; // Computed once per image to make it cover the frame
        let userZoom = 1; // User-controlled zoom (1.0 = default, slider range 0.6-2.5)

        // Pan state
        let panX = 0;
        let panY = 0;

        // Drag state
        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let mouseDownX = 0;
        let mouseDownY = 0;

        // Image natural dimensions (true pixel size, not scaled)
        let imgNaturalW = 0;
        let imgNaturalH = 0;

        // Other state
        let currentObjectURL = null;
        let imageChanged = false;
        let currentImageToken = 0; // Guard against stale onload callbacks

        const showError = (msg) => {
            if (errorEl) {
                errorEl.textContent = msg;
                errorEl.classList.remove("hidden");
            }
        };

        const hideError = () => errorEl?.classList.add("hidden");

        /**
         * Apply image transform using center-anchored positioning
         *
         * Scale model:
         * - baseCoverScale: computed once per image load (makes image cover the circle)
         * - userZoom: user-controlled via slider (default 1.0)
         * - finalScale = baseCoverScale * userZoom
         *
         * Transform formula:
         * transform: translate(-50%, -50%) translate(panX, panY) scale(finalScale)
         */
         const updateImageTransform = (animate = false) => {
            if (!img || !imgNaturalW || !imgNaturalH || !baseCoverScale) return;

            // Get REAL frame size from DOM (responsive)
            const { frameW, frameH } = getFrameSize();

            // Final scale = base cover scale * user zoom
            const finalScale = baseCoverScale * userZoom;

            // Calculate scaled dimensions
            const scaledW = imgNaturalW * finalScale;
            const scaledH = imgNaturalH * finalScale;

            // Calculate "freedom" to pan - CENTER-BASED COORDINATES
            // panX = 0, panY = 0 means IMAGE IS CENTERED (not stuck to left/top)
            
            // Calculate excess area (how much image extends beyond frame)
            const excessW = scaledW - frameW;
            const excessH = scaledH - frameH;

            // Calculate max pan based on excess
            // If image larger than container: maxPan = excess / 2
            // If image smaller: use SOFT BOUNDS so image doesn't feel stuck
            let maxPanX = Math.max(0, excessW / 2);
            let maxPanY = Math.max(0, excessH / 2);

            // SOFT BOUNDS: When zoomed out, still allow panning within frame
            // This prevents "stuck to left" feeling - image can move freely
            const softBoundX = frameW * 0.35;
            const softBoundY = frameH * 0.35;
            
            maxPanX = Math.max(maxPanX, softBoundX);
            maxPanY = Math.max(maxPanY, softBoundY);

            // Clamp pan values symmetrically around center
            panX = Math.max(-maxPanX, Math.min(maxPanX, panX));
            panY = Math.max(-maxPanY, Math.min(maxPanY, panY));

            // Apply styles - use TRUE natural dimensions, scale via transform
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

            // Always show blurred background (adds depth and fills zoomed-out empty space)
            if (blurContainer) {
                blurContainer.classList.remove('hidden');
            }
        };

        /**
         * Center image (reset pan to 0,0)
         */
        const centerImage = (animate = true) => {
            if (!imgNaturalW || !imgNaturalH) return;
            panX = 0;
            panY = 0;
            updateImageTransform(animate);
        };

        const showImageUI = () => {
            hasImage = true;
            imgContainer?.classList.remove("hidden");
            circleMask?.classList.remove("hidden"); // Show circular mask
            placeholder?.classList.add("hidden");
            actions?.classList.remove("opacity-0");
            actions?.classList.add("opacity-100");
            zoomContainer?.classList.remove("hidden");
            dropzone.style.cursor = "grab";
            if (hintEl) hintEl.textContent = "Geser foto untuk atur posisi";
            if (blurImg) blurImg.classList.remove('hidden'); // Show blur bg
        };

        const hideImageUI = () => {
            hasImage = false;
            isEditing = false;
            imgContainer?.classList.add("hidden");
            circleMask?.classList.add("hidden"); // Hide circular mask
            placeholder?.classList.remove("hidden");
            actions?.classList.add("opacity-0");
            actions?.classList.remove("opacity-100");
            zoomContainer?.classList.add("hidden");
            dropzone.style.cursor = "pointer";
            dropzone.style.cursor = "pointer";
            if (hintEl) hintEl.textContent = "Ini opsional kok, tapi bikin profilmu lebih hidup";
            if (blurImg) blurImg.classList.add('hidden');
        };

        /**
         * Reset all transform state for new image
         * Called before loading any new image to ensure clean slate
         */
        const resetTransformState = () => {
            // Reset scale state
            baseCoverScale = 1;
            userZoom = 1;

            // Reset pan position
            panX = 0;
            panY = 0;

            // Clear natural dimensions (will be set from new image)
            imgNaturalW = 0;
            imgNaturalH = 0;

            // Reset drag state
            isDragging = false;
            isEditing = false;
            dragStartX = 0;
            dragStartY = 0;
            mouseDownX = 0;
            mouseDownY = 0;

            // Reset zoom slider UI to default (100 = 1.0x)
            if (zoomSlider) zoomSlider.value = 100;

            // Increment token to invalidate any pending onload callbacks
            currentImageToken++;
        };

        /**
         * Load a new image file (upload or drag-drop)
         * Uses token guard to prevent stale onload callbacks from applying
         */
        const loadImage = (file) => {
            hideError();

            if (!ALLOWED_TYPES.includes(file.type)) {
                showError("Invalid format. Use JPG, PNG, or GIF.");
                return;
            }

            if (file.size > MAX_SIZE) {
                showError("File size must be under 5MB.");
                return;
            }

            // Clean up previous object URL
            if (currentObjectURL) {
                URL.revokeObjectURL(currentObjectURL);
                currentObjectURL = null;
            }

            // Hard reset ALL state (this also increments currentImageToken)
            resetTransformState();
            imageChanged = true;

            // Clear remove_avatar flag since user is uploading new image
            if (removeAvatarInput) {
                removeAvatarInput.value = "0";
                console.log("[Avatar] remove_avatar flag cleared (new upload)");
            }

            // Capture token for this load operation
            const loadToken = currentImageToken;

            // Clear cropped data
            if (croppedInput) croppedInput.value = "";

            // Clear old onload handler
            img.onload = null;

            // Hide during load and clear all styles
            img.style.opacity = "0";
            img.style.transform = "";
            img.style.width = "";
            img.style.height = "";

            // Clear src first
            img.src = "";
            if (blurImg) blurImg.src = "";

            // Create new object URL
            currentObjectURL = URL.createObjectURL(file);

            // Set up onload BEFORE setting src
            img.onload = () => {
                // Guard: only process if this is still the current image
                if (loadToken !== currentImageToken) {
                    console.log(
                        "[Avatar] Stale onload ignored, token mismatch"
                    );
                    return;
                }

                // Read TRUE natural dimensions
                imgNaturalW = img.naturalWidth;
                imgNaturalH = img.naturalHeight;

                // Get REAL frame size from DOM (responsive)
                const { frameW, frameH } = getFrameSize();

                // Compute cover scale ONCE: image must fill the banner
                baseCoverScale = Math.max(
                    frameW / imgNaturalW,
                    frameH / imgNaturalH
                );

                // Reset user zoom and pan to defaults
                userZoom = 1;
                panX = 0;
                panY = 0;
                if (zoomSlider) zoomSlider.value = 100;

                console.log("[Avatar] Image loaded:", {
                    naturalW: imgNaturalW,
                    naturalH: imgNaturalH,
                    baseCoverScale,
                    userZoom,
                    finalScale: baseCoverScale * userZoom,
                });

                // Apply transform ONCE with correct scale
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

                // Show UI
                showImageUI();

                // Fade in
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        img.style.transition = "opacity 0.3s ease";
                        img.style.opacity = "1";
                    });
                });
            };

            // Trigger load
            img.src = currentObjectURL;
            if (blurImg) blurImg.src = currentObjectURL; // Sync blur bg
        };

        /**
         * Initialize existing avatar image (from server)
         */
        const initExistingImage = () => {
            if (!hasImage || !img.src) return;

            // Wait for image to load
            if (img.complete && img.naturalWidth > 0) {
                setupExistingImage();
            } else {
                img.onload = setupExistingImage;
            }
        };

        const setupExistingImage = () => {
            // Read TRUE natural dimensions
            imgNaturalW = img.naturalWidth;
            imgNaturalH = img.naturalHeight;

            // Get REAL frame size from DOM (responsive)
            const { frameW, frameH } = getFrameSize();

            // Compute cover scale ONCE
            baseCoverScale = Math.max(
                frameW / imgNaturalW,
                frameH / imgNaturalH
            );

            // Reset user zoom and pan
            userZoom = 1;
            panX = 0;
            panY = 0;

            // Apply transform with correct scale
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

            if (hintEl) hintEl.textContent = "Drag to reposition your photo";
        };

        const reset = () => {
            if (currentObjectURL) {
                URL.revokeObjectURL(currentObjectURL);
                currentObjectURL = null;
            }

            input.value = "";
            img.src = "";
            if (blurImg) blurImg.src = "";
            img.style.width = "";
            img.style.height = "";
            img.style.left = "";
            img.style.top = "";
            img.style.transform = "";

            resetTransformState();
            imageChanged = true;

            // Set remove_avatar flag to signal backend to delete avatar
            if (removeAvatarInput) {
                removeAvatarInput.value = "1";
                console.log("[Avatar] remove_avatar flag set to 1");
            }

            if (croppedInput) croppedInput.value = "";
            hideImageUI();
            hideError();
        };

        // Initialize existing image if present
        initExistingImage();

        // Click to upload
        dropzone.addEventListener("click", (e) => {
            if (hasImage) return;
            if (isDragging || isEditing) return;
            input.click();
        });

        changeBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            input.click();
        });

        input.addEventListener("change", () => {
            const file = input.files?.[0];
            if (file) loadImage(file);
        });

        // Drag & drop
        let isDroppingFile = false;

        dropzone.addEventListener("dragenter", (e) => {
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

            const file = e.dataTransfer?.files?.[0];
            if (file && file.type.startsWith("image/")) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                loadImage(file);
            }
        });

        // Pan/drag image
        const startDrag = (clientX, clientY) => {
            if (!hasImage) return;
            mouseDownX = clientX;
            mouseDownY = clientY;
            dragStartX = clientX - panX;
            dragStartY = clientY - panY;
            isEditing = true;
        };

        const moveDrag = (clientX, clientY) => {
            if (!hasImage || !isEditing) return;

            if (!isDragging) {
                const dx = Math.abs(clientX - mouseDownX);
                const dy = Math.abs(clientY - mouseDownY);
                if (dx < DRAG_THRESHOLD && dy < DRAG_THRESHOLD) return;

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
                updateImageTransform(true);
            }
        };

        dropzone.addEventListener("mousedown", (e) => {
            if (!hasImage) return;
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

        dropzone.addEventListener(
            "touchstart",
            (e) => {
                if (!hasImage || e.touches.length !== 1) return;
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

        // Zoom slider - controls userZoom only (1.0 to 2.5 range)
        // Slider value 100 = userZoom 1.0, value 250 = userZoom 2.5
        zoomSlider?.addEventListener("input", () => {
            userZoom = Number(zoomSlider.value) / 100;
            updateImageTransform(false);
        });

        resetBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
            userZoom = 1;
            if (zoomSlider) zoomSlider.value = 100;
            centerImage(true);
        });

        deleteBtn?.addEventListener("click", (e) => {
            e.stopPropagation();
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

        dropzone.style.cursor = hasImage ? "grab" : "pointer";

        // Generate cropped avatar on form submit
        this.generateCroppedAvatar = () => {
            if (!imageChanged || !hasImage) return;

            const canvas = document.getElementById("avatar-canvas");
            if (!canvas || !img || !img.src) return;

            const ctx = canvas.getContext("2d");
            const CROP_SIZE = 256;
            const scaleFactor = CROP_SIZE / CONTAINER_SIZE;

            // Get current final scale
            const finalScale = baseCoverScale * userZoom;

            // Calculate scaled dimensions
            const imgWidth = imgNaturalW * finalScale;
            const imgHeight = imgNaturalH * finalScale;

            // Calculate draw position (center-anchored with pan offset)
            const centerX = CROP_SIZE / 2 + panX * scaleFactor;
            const centerY = CROP_SIZE / 2 + panY * scaleFactor;
            const drawX = centerX - (imgWidth * scaleFactor) / 2;
            const drawY = centerY - (imgHeight * scaleFactor) / 2;
            const drawW = imgWidth * scaleFactor;
            const drawH = imgHeight * scaleFactor;

            ctx.clearRect(0, 0, CROP_SIZE, CROP_SIZE);

            // NO circular clip - output is SQUARE (1:1)
            // Circle display is handled by CSS (rounded-full) only

            // Draw the blurred background first if needed
            // Actually, we want the WYSIWYG result.
            // If scale < cover, we see the blur behind.
            // Canvas MUST reflect that.
            
            // 1. Draw blurred background extended
            // OR... simplified: if user zoomed out, they get the whitespace/blur?
            // "Default image fills container... Zoom Out allowed... Empty space handling"
            // For the avatar *file* (the crop), do we save the blur?
            // The prompt says "One photo source only... Default image fills container (cover)".
            // If we save the "cropped square" we commit the zoom.
            // If the user zoomed out, we probably want to burn the blurred background into the avatar so it looks right everywhere.
            
            // Fill with black or white first?
            ctx.fillStyle = "#f1f5f9"; // slate-100
            ctx.fillRect(0, 0, CROP_SIZE, CROP_SIZE);

            // If zoomed out (diffW < 0 or diffH < 0), draw blurred background
            // We can approximate the blur by drawing the image scaled up and using filter
            // But canvas filter support varies. 
            // Simple approach: Draw image covering full canvas first (for the bg), then draw main image.
            
            // Draw BG (Cover)
             if (imgWidth < CROP_SIZE || imgHeight < CROP_SIZE) {
                // Calculate cover dimensions for the BG
                const bgScale = Math.max(CROP_SIZE / imgNaturalW, CROP_SIZE / imgNaturalH);
                const bgW = imgNaturalW * bgScale;
                const bgH = imgNaturalH * bgScale;
                const bgX = (CROP_SIZE - bgW) / 2;
                const bgY = (CROP_SIZE - bgH) / 2;
                
                ctx.save();
                ctx.filter = 'blur(10px)'; // Standard canvas filter
                // Scale up slightly to avoid edge artifacts
                ctx.drawImage(img, bgX - 10, bgY - 10, bgW + 20, bgH + 20);
                ctx.restore();
                
                // Overlay to darken like CSS
                ctx.fillStyle = "rgba(255, 255, 255, 0.1)";
                ctx.fillRect(0,0, CROP_SIZE, CROP_SIZE);
            }

            // Draw Main Image
            ctx.drawImage(img, drawX, drawY, drawW, drawH);

            try {
                const dataURL = canvas.toDataURL("image/jpeg", 0.9);
                if (croppedInput) croppedInput.value = dataURL;
            } catch (err) {
                console.error("Failed to generate cropped avatar:", err);
            }
        };
    }

    initBioCounter() {
        if (!this.bioInput || !this.bioCount) return;

        const updateCount = () => {
            this.bioCount.textContent = this.bioInput.value.length;
        };

        this.bioInput.addEventListener("input", updateCount);
        updateCount();
    }

    initMiddleNameToggle() {
        if (!this.middleNameToggle || !this.middleNameField) return;

        this.middleNameToggle.addEventListener("change", () => {
            if (this.middleNameToggle.checked) {
                this.middleNameField.classList.remove("hidden");
            } else {
                this.middleNameField.classList.add("hidden");
            }
        });
    }

    initFormSubmit() {
        if (!this.profileForm) return;

        this.profileForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            // Generate cropped avatar before submit
            if (this.generateCroppedAvatar) {
                this.generateCroppedAvatar();
            }

            const submitBtn = document.getElementById("save-profile-btn");
            const originalText = submitBtn?.textContent;

            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = "Saving...";
            }

            try {
                const formData = new FormData(this.profileForm);

                // Debug: Log form data keys
                console.log("[Profile] Form data keys:");
                for (const [key, value] of formData.entries()) {
                    if (key === "cropped_avatar") {
                        console.log(
                            `  ${key}: [base64 data, length=${value.length}]`
                        );
                    } else {
                        console.log(`  ${key}: ${value}`);
                    }
                }

                const response = await fetch(this.profileForm.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                const data = await response.json();

                // Debug: Log response
                console.log("[Profile] Response:", {
                    success: data.success,
                    avatar_url: data.avatar_url,
                    avatar_version: data.avatar_version,
                    avatar_changed: data.avatar_changed,
                });

                if (data.success) {
                    // Debug: Check avatar elements before update
                    const avatarImgs =
                        document.querySelectorAll("[data-avatar-img]");
                    const avatarFallbacks = document.querySelectorAll(
                        "[data-avatar-fallback]"
                    );
                    console.log("[Profile] Avatar elements found:", {
                        imgs: avatarImgs.length,
                        fallbacks: avatarFallbacks.length,
                    });

                    // Update all avatar instances across the page
                    ProfileManager.updateAllAvatars(
                        data.avatar_url,
                        data.avatar_version
                    );

                    // Show success feedback
                    ProfileManager.showToast(
                        "Profile updated successfully!",
                        "success"
                    );

                    // Hide edit form, show profile details
                    this.editSection?.classList.add("hidden");
                    this.profileDetails?.classList.remove("hidden");
                    this.editBtn?.classList.remove("hidden");

                    // Reset remove_avatar flag after successful save
                    const removeAvatarInput =
                        document.getElementById("remove_avatar");
                    if (removeAvatarInput) {
                        removeAvatarInput.value = "0";
                    }

                    // Reload page to show updated data after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    ProfileManager.showToast(
                        data.message || "Failed to update profile.",
                        "error"
                    );
                }
            } catch (err) {
                console.error("Profile update error:", err);
                ProfileManager.showToast(
                    "An error occurred. Please try again.",
                    "error"
                );
            } finally {
                // Reset button state
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            }
        });
    }

    /**
     * Update all avatar images across the page (navbar, profile header, etc.)
     * Uses data attributes to find avatar elements.
     * @param {string|null} avatarUrl - New avatar URL (already cache-busted), or null to show fallback
     * @param {number} version - Version timestamp for cache-busting (optional)
     */
    static updateAllAvatars(avatarUrl, version = null) {
        // Find all avatar images and fallbacks directly
        const avatarImgs = document.querySelectorAll("[data-avatar-img]");
        const avatarFallbacks = document.querySelectorAll(
            "[data-avatar-fallback]"
        );

        console.log("[Avatar] updateAllAvatars called:", {
            avatarUrl,
            version,
            imgsFound: avatarImgs.length,
            fallbacksFound: avatarFallbacks.length,
        });

        // Build final URL with cache-busting if needed
        let finalUrl = avatarUrl;
        if (avatarUrl && version && !avatarUrl.includes("?v=")) {
            finalUrl = avatarUrl + "?v=" + version;
        }

        if (finalUrl) {
            // Show images, hide fallbacks
            avatarImgs.forEach((img) => {
                console.log(
                    "[Avatar] Setting img src:",
                    img.dataset.avatarImg || "unknown"
                );
                img.src = finalUrl;
                img.classList.remove("hidden");
                img.style.display = "";
            });
            avatarFallbacks.forEach((fallback) => {
                fallback.classList.add("hidden");
                fallback.style.display = "none";
            });
        } else {
            // Hide images, show fallbacks (avatar was removed)
            avatarImgs.forEach((img) => {
                console.log(
                    "[Avatar] Hiding img:",
                    img.dataset.avatarImg || "unknown"
                );
                img.src = "";
                img.classList.add("hidden");
                img.style.display = "none";
            });
            avatarFallbacks.forEach((fallback) => {
                fallback.classList.remove("hidden");
                fallback.style.display = "flex";
            });
        }

        console.log("[Avatar] Updated", avatarImgs.length, "avatar images");
    }

    /**
     * Show a toast notification
     * @param {string} message - Message to display
     * @param {string} type - Toast type: success, error, info, warning
     */
    static showToast(message, type = "info") {
        // Map toast types to icon files
        const iconMap = {
            success: "check-circle.svg",
            error: "x-circle.svg",
            info: "info-circle.svg",
            warning: "warning.svg",
        };

        const iconFile = iconMap[type] || iconMap.info;
        const iconPath = `/assets/icons/${iconFile}`;

        // Check if toast container exists, create if not
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className = "toast-container";
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <img src="${iconPath}" class="toast-icon-img" alt="" onerror="this.style.display='none'">
            </div>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.add("toast-show");
        });

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove("toast-show");
            toast.classList.add("toast-hide");
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new ProfileManager();
});

export default ProfileManager;
