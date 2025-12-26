/**
 * Banner Avatar Editor
 * Pan/zoom editor for profile banner with circle overlay preview
 */

class BannerEditor {
    constructor() {
        this.container = document.getElementById('banner-container');
        this.image = document.getElementById('banner-image');
        this.editBtn = document.getElementById('edit-banner-btn');
        this.saveBtn = document.getElementById('save-edit-btn');
        this.cancelBtn = document.getElementById('cancel-edit-btn');
        this.zoomSlider = document.getElementById('zoom-slider');
        this.circleOverlay = document.getElementById('circle-overlay');
        this.editControls = document.getElementById('edit-controls');
        this.editIndicator = document.getElementById('edit-indicator');

        if (!this.container || !this.image) return;

        // Get initial focus from data attribute
        const focusData = this.container.dataset.avatarFocus;
        this.initialFocus = focusData ? JSON.parse(focusData) : { x: 0.5, y: 0.5, zoom: 1.0 };
        this.currentFocus = { ...this.initialFocus };
        this.saveUrl = this.container.dataset.saveUrl;
        this.csrfToken = this.container.dataset.csrf;

        // State
        this.isEditing = false;
        this.isDragging = false;
        this.dragStart = { x: 0, y: 0 };
        this.focusStart = { x: 0, y: 0 };

        this.init();
    }

    init() {
        // Edit button
        this.editBtn?.addEventListener('click', () => this.startEdit());

        // Save/Cancel
        this.saveBtn?.addEventListener('click', () => this.save());
        this.cancelBtn?.addEventListener('click', () => this.cancel());

        // Zoom slider
        this.zoomSlider?.addEventListener('input', () => {
            this.currentFocus.zoom = parseInt(this.zoomSlider.value) / 100;
            this.updateImageStyle();
        });

        // Pan (drag) events
        this.container.addEventListener('mousedown', (e) => this.startDrag(e));
        document.addEventListener('mousemove', (e) => this.drag(e));
        document.addEventListener('mouseup', () => this.endDrag());

        // Touch events
        this.container.addEventListener('touchstart', (e) => this.startDrag(e), { passive: false });
        document.addEventListener('touchmove', (e) => this.drag(e), { passive: false });
        document.addEventListener('touchend', () => this.endDrag());

        // Apply initial style
        this.updateImageStyle();
    }

    startEdit() {
        this.isEditing = true;
        this.container.style.cursor = 'grab';

        // Show overlay and controls
        this.circleOverlay?.classList.remove('opacity-0');
        this.circleOverlay?.classList.add('opacity-100');
        this.editControls?.classList.remove('opacity-0', 'pointer-events-none');
        this.editControls?.classList.add('opacity-100', 'pointer-events-auto');
        this.editIndicator?.classList.remove('opacity-0');
        this.editIndicator?.classList.add('opacity-100');
        this.editBtn?.classList.add('hidden');

        // Set slider value
        if (this.zoomSlider) {
            this.zoomSlider.value = Math.round(this.currentFocus.zoom * 100);
        }
    }

    endEdit() {
        this.isEditing = false;
        this.container.style.cursor = '';

        // Hide overlay and controls
        this.circleOverlay?.classList.add('opacity-0');
        this.circleOverlay?.classList.remove('opacity-100');
        this.editControls?.classList.add('opacity-0', 'pointer-events-none');
        this.editControls?.classList.remove('opacity-100', 'pointer-events-auto');
        this.editIndicator?.classList.add('opacity-0');
        this.editIndicator?.classList.remove('opacity-100');
        this.editBtn?.classList.remove('hidden');
    }

    startDrag(e) {
        if (!this.isEditing) return;

        e.preventDefault();
        this.isDragging = true;
        this.container.style.cursor = 'grabbing';

        const pos = this.getEventPosition(e);
        this.dragStart = pos;
        this.focusStart = { ...this.currentFocus };
    }

    drag(e) {
        if (!this.isDragging) return;

        e.preventDefault();
        const pos = this.getEventPosition(e);
        const containerRect = this.container.getBoundingClientRect();

        // Calculate delta as percentage of container
        const deltaX = (pos.x - this.dragStart.x) / containerRect.width;
        const deltaY = (pos.y - this.dragStart.y) / containerRect.height;

        // Invert direction (moving mouse right = focal point moves left)
        this.currentFocus.x = Math.max(0, Math.min(1, this.focusStart.x - deltaX));
        this.currentFocus.y = Math.max(0, Math.min(1, this.focusStart.y - deltaY));

        this.updateImageStyle();
    }

    endDrag() {
        if (!this.isDragging) return;
        this.isDragging = false;
        this.container.style.cursor = this.isEditing ? 'grab' : '';
    }

    getEventPosition(e) {
        if (e.touches && e.touches.length > 0) {
            return { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }
        return { x: e.clientX, y: e.clientY };
    }

    updateImageStyle() {
        if (!this.image) return;

        const posX = this.currentFocus.x * 100;
        const posY = this.currentFocus.y * 100;
        const zoom = this.currentFocus.zoom;

        this.image.style.objectPosition = `${posX}% ${posY}%`;
        this.image.style.transform = `scale(${zoom})`;
        this.image.style.transformOrigin = `${posX}% ${posY}%`;
    }

    async save() {
        try {
            this.saveBtn.disabled = true;
            this.saveBtn.textContent = 'Menyimpan...';

            const response = await fetch(this.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(this.currentFocus),
            });

            const data = await response.json();

            if (data.success) {
                this.initialFocus = { ...this.currentFocus };
                this.endEdit();
                // Show success toast
                this.showToast('Posisi avatar tersimpan!', 'success');
            } else {
                this.showToast(data.message || 'Gagal menyimpan', 'error');
            }
        } catch (err) {
            console.error('Save error:', err);
            this.showToast('Terjadi kesalahan', 'error');
        } finally {
            this.saveBtn.disabled = false;
            this.saveBtn.textContent = 'Simpan';
        }
    }

    cancel() {
        // Restore to initial values
        this.currentFocus = { ...this.initialFocus };
        this.updateImageStyle();
        this.endEdit();
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 px-6 py-3 rounded-full text-sm font-medium shadow-lg z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
        }`;
        toast.textContent = message;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    new BannerEditor();
});

export default BannerEditor;
