/**
 * Avatar Editor Module
 * 
 * Handles photo upload, pan, zoom, and cropping for profile settings.
 * Cropping uses CENTER CIRCLE (same as onboarding) for navbar avatar.
 */

export class AvatarEditor {
    constructor(options = {}) {
        // Core elements
        this.dropzone = document.getElementById('avatar-dropzone');
        this.fileInput = document.getElementById('profile_picture');
        this.img = document.getElementById('avatar-img');
        this.blurImg = document.getElementById('avatar-blur-img');
        this.imgContainer = document.getElementById('avatar-img-container');
        this.blurContainer = document.getElementById('avatar-blur-bg-container');
        this.placeholder = document.getElementById('avatar-placeholder');
        this.circleMask = document.getElementById('avatar-circle-mask');
        this.dropOverlay = document.getElementById('avatar-drop-overlay');
        this.editOverlay = document.getElementById('avatar-edit-overlay');
        this.zoomContainer = document.getElementById('avatar-zoom-container');
        this.zoomSlider = document.getElementById('avatar-zoom');
        this.removeInput = document.getElementById('remove_avatar');
        this.croppedInput = document.getElementById('cropped_avatar');
        this.canvas = document.getElementById('avatar-canvas');
        this.errorDisplay = document.getElementById('avatar-error');
        this.form = document.getElementById('profile-form');

        // Buttons
        this.changeBtn = document.getElementById('avatar-change');
        this.resetBtn = document.getElementById('avatar-reset');
        this.deleteBtn = document.getElementById('avatar-delete');

        // Constants - SAME AS ONBOARDING
        this.ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        this.MAX_SIZE = 5 * 1024 * 1024; // 5MB
        this.CIRCLE_RADIUS = 80; // Circle for navbar avatar (same as onboarding)
        this.CIRCLE_DIAMETER = this.CIRCLE_RADIUS * 2; // 160px
        this.OUTPUT_SIZE = 256; // Canvas output size

        // State
        this.currentObjectURL = null;
        this.isDragging = false;
        this.dragStart = { x: 0, y: 0 };
        this.panX = 0;
        this.panY = 0;
        this.panStartX = 0;
        this.panStartY = 0;
        this.baseCoverScale = 1;
        this.userZoom = 1;
        this.imgNaturalW = 0;
        this.imgNaturalH = 0;
        this.hasExistingImage = false;
        this.isNewUpload = false;
        this.hasEdits = false; // Track if user made any changes (pan/zoom)

        this.init();
    }

    init() {
        if (!this.dropzone) return;

        // Check for existing image
        const imgSrc = this.img?.getAttribute('src');
        this.hasExistingImage = imgSrc && imgSrc !== '' && !imgSrc.includes('undefined');

        if (this.hasExistingImage && this.img?.src) {
            this.initExistingImage();
        }

        // File input
        this.fileInput?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) this.handleFile(file);
        });

        // Dropzone click
        this.dropzone.addEventListener('click', (e) => {
            if (e.target.closest('button')) return;
            if (!this.hasExistingImage || e.target.closest('#avatar-placeholder')) {
                this.fileInput?.click();
            }
        });

        this.setupDragDrop();
        this.setupPanDrag();
        this.setupZoom();
        this.setupButtons();
        this.setupFormSubmit();
    }

    // ========================================
    // HELPER: Get frame size from DOM
    // ========================================

    getFrameSize() {
        const rect = this.dropzone.getBoundingClientRect();
        return { frameW: rect.width, frameH: rect.height };
    }

    // ========================================
    // FORM SUBMIT - CROP FROM CENTER CIRCLE
    // ========================================

    setupFormSubmit() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            // Generate crop if: new upload OR user made edits (pan/zoom)
            if ((this.isNewUpload || this.hasEdits) && this.img && this.img.src && this.hasExistingImage) {
                this.generateCroppedAvatar();
            }
        });
    }

    /**
     * Generate cropped image from FULL VISIBLE FRAME (banner).
     * The navbar avatar is extracted from CENTER of this banner on display.
     * Output: 768x256 (3:1 aspect ratio)
     */
    generateCroppedAvatar() {
        if (!this.canvas || !this.img || !this.croppedInput) return;
        if (!this.imgNaturalW || !this.imgNaturalH || !this.baseCoverScale) return;

        const ctx = this.canvas.getContext('2d');
        if (!ctx) return;

        // Get frame size from DOM
        const { frameW, frameH } = this.getFrameSize();

        // Output size for banner (3:1 aspect)
        const OUTPUT_W = 768;
        const OUTPUT_H = 256;

        // Set canvas size
        this.canvas.width = OUTPUT_W;
        this.canvas.height = OUTPUT_H;

        // Final scale = baseCoverScale * userZoom
        const finalScale = this.baseCoverScale * this.userZoom;

        // Scaled image dimensions
        const scaledW = this.imgNaturalW * finalScale;
        const scaledH = this.imgNaturalH * finalScale;

        // Frame center
        const frameCenterX = frameW / 2;
        const frameCenterY = frameH / 2;

        // Image center in frame coordinates (with pan offset)
        const imgCenterX = frameCenterX + this.panX;
        const imgCenterY = frameCenterY + this.panY;

        // Image top-left corner in frame coordinates
        const imgLeft = imgCenterX - scaledW / 2;
        const imgTop = imgCenterY - scaledH / 2;

        // FULL FRAME area to crop (the entire visible frame, not just circle)
        const frameLeft = 0;
        const frameTop = 0;

        // Calculate source rectangle in original image coordinates
        const srcX = (frameLeft - imgLeft) / finalScale;
        const srcY = (frameTop - imgTop) / finalScale;
        const srcW = frameW / finalScale;
        const srcH = frameH / finalScale;

        // Clear and draw
        ctx.clearRect(0, 0, OUTPUT_W, OUTPUT_H);

        try {
            ctx.drawImage(
                this.img,
                srcX, srcY, srcW, srcH,
                0, 0, OUTPUT_W, OUTPUT_H
            );

            const dataURL = this.canvas.toDataURL('image/jpeg', 0.9);
            this.croppedInput.value = dataURL;
        } catch (err) {
            console.error('Failed to generate cropped avatar:', err);
            this.croppedInput.value = '';
        }
    }

    // ========================================
    // EXISTING IMAGE
    // ========================================

    initExistingImage() {
        if (!this.img) return;

        if (this.img.complete && this.img.naturalWidth > 0) {
            this.applyInitialScale();
        } else {
            this.img.onload = () => this.applyInitialScale();
        }
    }

    applyInitialScale() {
        if (!this.dropzone || !this.img) return;

        const { frameW, frameH } = this.getFrameSize();
        const imgW = this.img.naturalWidth;
        const imgH = this.img.naturalHeight;

        if (imgW === 0 || imgH === 0) return;

        this.imgNaturalW = imgW;
        this.imgNaturalH = imgH;

        // Calculate scale to cover frame (SAME AS ONBOARDING)
        this.baseCoverScale = Math.max(frameW / imgW, frameH / imgH);
        this.userZoom = 1;
        this.panX = 0;
        this.panY = 0;

        // Apply styles
        this.img.style.width = imgW + 'px';
        this.img.style.height = imgH + 'px';
        this.updateTransform();

        if (this.zoomSlider) this.zoomSlider.value = 100;
        this.hasExistingImage = true;
    }

    // ========================================
    // FILE HANDLING
    // ========================================

    handleFile(file) {
        if (!this.ALLOWED_TYPES.includes(file.type)) {
            this.showError('Hanya JPG, PNG, atau GIF yang diperbolehkan');
            return;
        }

        if (file.size > this.MAX_SIZE) {
            this.showError('File terlalu besar. Maksimal 5MB.');
            return;
        }

        this.hideError();

        if (this.currentObjectURL) {
            URL.revokeObjectURL(this.currentObjectURL);
        }

        // Reset state for new image
        this.baseCoverScale = 1;
        this.userZoom = 1;
        this.panX = 0;
        this.panY = 0;
        this.imgNaturalW = 0;
        this.imgNaturalH = 0;
        if (this.zoomSlider) this.zoomSlider.value = 100;

        this.currentObjectURL = URL.createObjectURL(file);
        this.isNewUpload = true;

        if (this.img) {
            this.img.src = '';
            this.img.src = this.currentObjectURL;
            this.img.onload = () => this.onNewImageLoaded();
        }
    }

    onNewImageLoaded() {
        const { frameW, frameH } = this.getFrameSize();
        const imgW = this.img.naturalWidth;
        const imgH = this.img.naturalHeight;

        this.imgNaturalW = imgW;
        this.imgNaturalH = imgH;

        // Show elements
        this.imgContainer?.classList.remove('hidden');
        this.blurContainer?.classList.remove('hidden');
        this.placeholder?.classList.add('hidden');
        this.circleMask?.classList.remove('hidden');
        this.zoomContainer?.classList.remove('hidden');

        // Update blur
        if (this.blurImg) {
            this.blurImg.src = this.currentObjectURL;
            this.blurImg.classList.remove('hidden');
        }

        // Calculate scale (SAME AS ONBOARDING)
        this.baseCoverScale = Math.max(frameW / imgW, frameH / imgH);
        this.userZoom = 1;
        this.panX = 0;
        this.panY = 0;

        // Apply styles
        this.img.style.width = imgW + 'px';
        this.img.style.height = imgH + 'px';
        this.updateTransform();

        if (this.zoomSlider) this.zoomSlider.value = 100;
        if (this.removeInput) this.removeInput.value = '0';

        this.hasExistingImage = true;
    }

    // ========================================
    // DRAG & DROP
    // ========================================

    setupDragDrop() {
        if (!this.dropzone) return;

        this.dropzone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.dropOverlay?.classList.remove('opacity-0');
        });

        this.dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
        });

        this.dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.dropOverlay?.classList.add('opacity-0');
        });

        this.dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.dropOverlay?.classList.add('opacity-0');
            const file = e.dataTransfer.files[0];
            if (file) this.handleFile(file);
        });
    }

    // ========================================
    // PAN/DRAG
    // ========================================

    setupPanDrag() {
        if (!this.imgContainer) return;

        this.imgContainer.addEventListener('mousedown', (e) => this.startPan(e));
        document.addEventListener('mousemove', (e) => this.onPan(e));
        document.addEventListener('mouseup', () => this.endPan());

        this.imgContainer.addEventListener('touchstart', (e) => this.startPan(e), { passive: false });
        document.addEventListener('touchmove', (e) => this.onPan(e), { passive: false });
        document.addEventListener('touchend', () => this.endPan());

        this.imgContainer.addEventListener('mouseenter', () => {
            if (this.hasExistingImage) {
                this.editOverlay?.classList.remove('opacity-0');
            }
        });
        this.imgContainer.addEventListener('mouseleave', () => {
            if (!this.isDragging) {
                this.editOverlay?.classList.add('opacity-0');
            }
        });
    }

    startPan(e) {
        if (!this.hasExistingImage) return;
        e.preventDefault();

        this.isDragging = true;
        this.imgContainer.style.cursor = 'grabbing';

        const pos = this.getEventPosition(e);
        this.dragStart = pos;
        this.panStartX = this.panX;
        this.panStartY = this.panY;
    }

    onPan(e) {
        if (!this.isDragging) return;
        e.preventDefault();

        const pos = this.getEventPosition(e);

        this.panX = this.panStartX + (pos.x - this.dragStart.x);
        this.panY = this.panStartY + (pos.y - this.dragStart.y);

        this.hasEdits = true; // Mark as edited
        this.clampPan();
        this.updateTransform();
    }

    endPan() {
        if (!this.isDragging) return;
        this.isDragging = false;
        this.imgContainer.style.cursor = 'grab';
        this.editOverlay?.classList.add('opacity-0');
    }

    getEventPosition(e) {
        if (e.touches && e.touches.length > 0) {
            return { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }
        return { x: e.clientX, y: e.clientY };
    }

    // ========================================
    // ZOOM
    // ========================================

    setupZoom() {
        if (!this.zoomSlider) return;

        this.zoomSlider.addEventListener('input', () => {
            // Convert slider value (60-200) to userZoom (0.6-2.0)
            this.userZoom = parseInt(this.zoomSlider.value) / 100;
            this.hasEdits = true; // Mark as edited
            this.clampPan();
            this.updateTransform();
        });
    }

    /**
     * Clamp pan values with SOFT BOUNDS (same as onboarding)
     */
    clampPan() {
        if (!this.dropzone || !this.imgNaturalW || !this.imgNaturalH) return;

        const { frameW, frameH } = this.getFrameSize();
        const finalScale = this.baseCoverScale * this.userZoom;

        const scaledW = this.imgNaturalW * finalScale;
        const scaledH = this.imgNaturalH * finalScale;

        const excessW = scaledW - frameW;
        const excessH = scaledH - frameH;

        // Calculate max pan with soft bounds (SAME AS ONBOARDING)
        let maxPanX = Math.max(0, excessW / 2);
        let maxPanY = Math.max(0, excessH / 2);

        // Soft bounds: allow some panning even when zoomed out
        const softBoundX = frameW * 0.35;
        const softBoundY = frameH * 0.35;
        maxPanX = Math.max(maxPanX, softBoundX);
        maxPanY = Math.max(maxPanY, softBoundY);

        this.panX = Math.max(-maxPanX, Math.min(maxPanX, this.panX));
        this.panY = Math.max(-maxPanY, Math.min(maxPanY, this.panY));
    }

    /**
     * Apply CENTER-BASED transform (same as onboarding)
     */
    updateTransform() {
        if (!this.img || !this.imgNaturalW || !this.imgNaturalH) return;

        const finalScale = this.baseCoverScale * this.userZoom;

        this.img.style.position = 'absolute';
        this.img.style.left = '50%';
        this.img.style.top = '50%';
        this.img.style.transformOrigin = 'center';
        this.img.style.maxWidth = 'none';
        this.img.style.maxHeight = 'none';
        this.img.style.objectFit = 'none';
        this.img.style.transform = `translate(-50%, -50%) translate(${this.panX}px, ${this.panY}px) scale(${finalScale})`;
    }

    // ========================================
    // BUTTONS
    // ========================================

    setupButtons() {
        this.changeBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (this.fileInput) this.fileInput.value = ""; // penting biar same-file ke-trigger
            this.fileInput?.click();
        });


        this.resetBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.userZoom = 1;
            this.panX = 0;
            this.panY = 0;
            if (this.zoomSlider) this.zoomSlider.value = 100;
            this.updateTransform();
        });

        this.deleteBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeImage();
        });
    }

    removeImage() {
        if (this.removeInput) this.removeInput.value = '1';

        this.imgContainer?.classList.add('hidden');
        this.blurContainer?.classList.add('hidden');
        this.circleMask?.classList.add('hidden');
        this.zoomContainer?.classList.add('hidden');

        this.placeholder?.classList.remove('hidden');

        if (this.img) this.img.src = '';
        if (this.blurImg) this.blurImg.src = '';
        if (this.fileInput) this.fileInput.value = '';
        if (this.croppedInput) this.croppedInput.value = '';

        if (this.currentObjectURL) {
            URL.revokeObjectURL(this.currentObjectURL);
            this.currentObjectURL = null;
        }

        this.hasExistingImage = false;
        this.isNewUpload = false;
    }

    showError(message) {
        if (this.errorDisplay) {
            this.errorDisplay.textContent = message;
            this.errorDisplay.classList.remove('hidden');
        }
    }

    hideError() {
        if (this.errorDisplay) {
            this.errorDisplay.classList.add('hidden');
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    // Prevent double initialization on onboarding page (handled by OnboardingManager)
    if (document.getElementById('onboarding-form')) return;

    if (document.getElementById('avatar-dropzone')) {
        window.avatarEditor = new AvatarEditor();
    }
});
