/**
 * Avatar Crop Component
 * Handles single image cropping for avatar (square, 1:1)
 */

class AvatarCropManager {
    constructor() {
        this.modal = document.getElementById("crop-modal");
        this.cropImage = document.getElementById("crop-image");
        this.fileInput = document.getElementById("profile_picture");

        if (!this.modal || !this.cropImage || !this.fileInput) return;

        this.avatarInput = document.getElementById("cropped_avatar");
        this.removeInput = document.getElementById("remove_avatar");
        this.avatarPreviewContainer = document.getElementById(
            "avatar-preview-container"
        );

        this.saveBtn = document.getElementById("crop-save-btn");
        this.cancelBtn = document.getElementById("crop-cancel-btn");
        this.removeBtn = document.getElementById("remove-avatar-btn");

        this.originalImageUrl = null;
        this.avatarCropData = null;

        // Crop state
        this.scale = 1;
        this.panX = 0;
        this.panY = 0;
        this.isDragging = false;
        this.dragStartX = 0;
        this.dragStartY = 0;
        this.imgNaturalW = 0;
        this.imgNaturalH = 0;

        this.init();
    }

    init() {
        this.fileInput.addEventListener("change", (e) =>
            this.handleFileSelect(e)
        );
        this.cancelBtn?.addEventListener("click", () => this.closeModal());
        this.saveBtn?.addEventListener("click", () => this.handleSave());
        this.removeBtn?.addEventListener("click", () => this.handleRemove());

        // Drag events on crop image
        this.cropImage.addEventListener("mousedown", (e) => this.startDrag(e));
        document.addEventListener("mousemove", (e) => this.moveDrag(e));
        document.addEventListener("mouseup", () => this.endDrag());

        // Touch events
        this.cropImage.addEventListener(
            "touchstart",
            (e) => this.startDrag(e),
            { passive: false }
        );
        document.addEventListener("touchmove", (e) => this.moveDrag(e), {
            passive: false,
        });
        document.addEventListener("touchend", () => this.endDrag());

        // Wheel zoom
        this.cropImage.addEventListener("wheel", (e) => this.handleZoom(e), {
            passive: false,
        });
    }

    handleFileSelect(e) {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!file.type.startsWith("image/")) {
            alert("Please select an image file.");
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert("File size must be under 5MB.");
            return;
        }

        if (this.originalImageUrl) {
            URL.revokeObjectURL(this.originalImageUrl);
        }

        this.originalImageUrl = URL.createObjectURL(file);
        this.avatarCropData = null;

        this.openModal();
    }

    openModal() {
        this.modal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
        this.loadImage();
    }

    closeModal() {
        this.modal.classList.add("hidden");
        document.body.style.overflow = "";
        this.resetCropState();
    }

    loadImage() {
        this.cropImage.onload = () => {
            this.imgNaturalW = this.cropImage.naturalWidth;
            this.imgNaturalH = this.cropImage.naturalHeight;
            this.resetCropState();
            this.updateImageTransform();
        };
        this.cropImage.src = this.originalImageUrl;
    }

    resetCropState() {
        this.scale = 1;
        this.panX = 0;
        this.panY = 0;
    }

    updateImageTransform() {
        const containerRect =
            this.cropImage.parentElement.getBoundingClientRect();
        const aspectRatio = 1; // Square crop for avatar

        // Calculate crop area size
        const cropSize = Math.min(
            containerRect.width * 0.8,
            containerRect.height * 0.8
        );
        const cropW = cropSize;
        const cropH = cropSize;

        // Calculate base scale to cover crop area
        const baseScale = Math.max(
            cropW / this.imgNaturalW,
            cropH / this.imgNaturalH
        );
        const finalScale = baseScale * this.scale;

        // Apply transform
        this.cropImage.style.transform = `translate(${this.panX}px, ${this.panY}px) scale(${finalScale})`;
        this.cropImage.style.cursor = "grab";
    }

    startDrag(e) {
        e.preventDefault();
        this.isDragging = true;
        this.cropImage.style.cursor = "grabbing";

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        this.dragStartX = clientX - this.panX;
        this.dragStartY = clientY - this.panY;
    }

    moveDrag(e) {
        if (!this.isDragging) return;

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        this.panX = clientX - this.dragStartX;
        this.panY = clientY - this.dragStartY;

        this.updateImageTransform();
    }

    endDrag() {
        this.isDragging = false;
        this.cropImage.style.cursor = "grab";
    }

    handleZoom(e) {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        this.scale = Math.max(1, Math.min(3, this.scale + delta));
        this.updateImageTransform();
    }

    generateCroppedImage(outputSize) {
        return new Promise((resolve) => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            canvas.width = outputSize;
            canvas.height = outputSize;

            const containerRect =
                this.cropImage.parentElement.getBoundingClientRect();
            const imgRect = this.cropImage.getBoundingClientRect();

            // Calculate crop area in container (square)
            const cropSize = Math.min(
                containerRect.width * 0.8,
                containerRect.height * 0.8
            );
            const cropX = (containerRect.width - cropSize) / 2;
            const cropY = (containerRect.height - cropSize) / 2;

            // Calculate source coordinates
            const scaleX = this.imgNaturalW / imgRect.width;
            const scaleY = this.imgNaturalH / imgRect.height;

            const srcX = (cropX - (imgRect.left - containerRect.left)) * scaleX;
            const srcY = (cropY - (imgRect.top - containerRect.top)) * scaleY;
            const srcW = cropSize * scaleX;
            const srcH = cropSize * scaleY;

            ctx.drawImage(
                this.cropImage,
                srcX,
                srcY,
                srcW,
                srcH,
                0,
                0,
                outputSize,
                outputSize
            );

            resolve(canvas.toDataURL("image/jpeg", 0.9));
        });
    }

    async handleSave() {
        this.avatarCropData = await this.generateCroppedImage(512);
        this.applyChanges();
        this.closeModal();
    }

    applyChanges() {
        // Set hidden input
        if (this.avatarInput)
            this.avatarInput.value = this.avatarCropData || "";
        if (this.removeInput) this.removeInput.value = "0";

        // Update preview
        this.updatePreview();
    }

    updatePreview() {
        if (this.avatarCropData && this.avatarPreviewContainer) {
            this.avatarPreviewContainer.innerHTML = `<img src="${this.avatarCropData}" alt="Avatar" class="w-full h-full object-cover" id="avatar-preview">`;
        }
    }

    handleRemove() {
        if (this.avatarInput) this.avatarInput.value = "";
        if (this.removeInput) this.removeInput.value = "1";

        // Reset preview
        if (this.avatarPreviewContainer) {
            const initial = this.avatarPreviewContainer.dataset.initial || "U";
            this.avatarPreviewContainer.innerHTML = `<span class="text-3xl font-bold text-slate-400" id="avatar-placeholder">${initial}</span>`;
        }

        this.avatarCropData = null;

        // Hide remove button
        if (this.removeBtn) {
            this.removeBtn.style.display = "none";
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new AvatarCropManager();
});

export default AvatarCropManager;
