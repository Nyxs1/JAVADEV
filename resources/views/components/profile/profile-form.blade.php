@props(['user'])

@php
    $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
    $displayUsername = ltrim($user->username, '@');
@endphp

<div class="profile-form-container" id="profile-form-container">
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-form">
        @csrf
        @method('PUT')

        {{-- VERTICAL LAYOUT MIRRORING PROFILE PAGE --}}
        <div class="flex flex-col gap-6 max-w-3xl mx-auto">

            {{-- ======================================= --}}
            {{-- CARD 1: COVER PHOTO EDITOR --}}
            {{-- ======================================= --}}
            <div class="relative">
                {{-- Cover Frame (SAME as Profile) --}}
                <x-profile.photo-frame 
                    :src="$hasAvatar ? $user->avatar_url : null"
                    :alt="$user->full_name ?: $displayUsername"
                    :fallback-initial="substr($displayUsername, 0, 1)"
                    :show-editor="true"
                    :has-image="$hasAvatar"
                />

                {{-- Hidden Inputs --}}
                <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/jpg,image/png,image/gif" class="hidden">
                <input type="hidden" name="cropped_avatar" id="cropped_avatar" value="">
                <input type="hidden" name="remove_avatar" id="remove_avatar" value="0">
                <canvas id="avatar-canvas" class="hidden" width="256" height="256"></canvas>
            </div>

            {{-- Zoom Controls --}}
            <div id="avatar-zoom-container" class="flex items-center justify-center gap-4 {{ $hasAvatar ? '' : 'hidden' }}">
                <button type="button" id="avatar-change" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Change
                </button>
                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                    <input type="range" id="avatar-zoom" min="60" max="250" value="100" class="w-32 h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-blue-600">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                </div>
                <button type="button" id="avatar-reset" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset
                </button>
                <button type="button" id="avatar-delete" class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Remove
                </button>
            </div>
            <p id="avatar-error" class="text-center text-sm text-red-500 hidden"></p>

            {{-- ======================================= --}}
            {{-- CARD 2: IDENTITY EDITOR --}}
            {{-- ======================================= --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-5">Identity</h3>
                
                <div class="space-y-4">
                    {{-- Full Name --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>

                    {{-- Middle Name --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="show-middle-name" class="rounded border-slate-300 text-blue-600" {{ $user->middle_name ? 'checked' : '' }}>
                            <label for="show-middle-name" class="text-sm text-slate-600">I have a middle name</label>
                        </div>
                        <div id="middle-name-field" class="{{ $user->middle_name ? '' : 'hidden' }}">
                            <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" placeholder="Middle name"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label for="bio" class="block text-sm font-medium text-slate-700 mb-1">Bio</label>
                        <textarea name="bio" id="bio" rows="3" placeholder="Tell us about yourself..." maxlength="160"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 resize-none">{{ old('bio', $user->bio) }}</textarea>
                        <div class="text-right text-xs text-slate-400 mt-1"><span id="bio-count">{{ strlen($user->bio ?? '') }}</span>/160</div>
                    </div>
                </div>
            </div>

            {{-- ======================================= --}}
            {{-- FORM ACTIONS --}}
            {{-- ======================================= --}}
            <div class="flex justify-between items-center bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                <a href="{{ route('profile.index') }}" class="px-5 py-2.5 text-slate-600 font-medium hover:text-slate-900 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="save-profile-btn" class="px-8 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium shadow-sm hover:shadow-md">
                    Save Profile
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Bio Counter
        const bioInput = document.getElementById('bio');
        const bioCount = document.getElementById('bio-count');
        if (bioInput && bioCount) {
            bioInput.addEventListener('input', () => {
                bioCount.textContent = bioInput.value.length;
            });
        }

        // Middle Name Toggle
        const middleToggle = document.getElementById('show-middle-name');
        const middleField = document.getElementById('middle-name-field');
        if (middleToggle && middleField) {
            middleToggle.addEventListener('change', () => {
                middleField.classList.toggle('hidden', !middleToggle.checked);
            });
        }

        // ========================================
        // PHOTO DROPZONE + EDITOR
        // ========================================
        const dropzone = document.getElementById('avatar-dropzone');
        const fileInput = document.getElementById('profile_picture');
        const img = document.getElementById('avatar-img');
        const blurImg = document.getElementById('avatar-blur-img');
        const imgContainer = document.getElementById('avatar-img-container');
        const blurContainer = document.getElementById('avatar-blur-bg-container');
        const placeholder = document.getElementById('avatar-placeholder');
        const circleMask = document.getElementById('avatar-circle-mask');
        const dropOverlay = document.getElementById('avatar-drop-overlay');
        const zoomContainer = document.getElementById('avatar-zoom-container');
        const zoomSlider = document.getElementById('avatar-zoom');
        const removeInput = document.getElementById('remove_avatar');
        const croppedInput = document.getElementById('cropped_avatar');
        
        // Buttons
        const changeBtn = document.getElementById('avatar-change');
        const resetBtn = document.getElementById('avatar-reset');
        const deleteBtn = document.getElementById('avatar-delete');

        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const MAX_SIZE = 5 * 1024 * 1024; // 5MB
        let currentObjectURL = null;

        // --- DROPZONE CLICK -> OPEN FILE PICKER ---
        if (dropzone && fileInput) {
            dropzone.addEventListener('click', (e) => {
                // Don't trigger if clicking on buttons or when image exists (let pan work)
                if (e.target.closest('button')) return;
                fileInput.click();
            });
        }

        // --- FILE INPUT CHANGE -> PREVIEW ---
        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) handleFile(file);
            });
        }

        // --- DRAG & DROP EVENTS ---
        if (dropzone) {
            dropzone.addEventListener('dragenter', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (dropOverlay) dropOverlay.classList.remove('opacity-0');
            });

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (dropOverlay) dropOverlay.classList.remove('opacity-0');
            });

            dropzone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (dropOverlay) dropOverlay.classList.add('opacity-0');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (dropOverlay) dropOverlay.classList.add('opacity-0');
                
                const file = e.dataTransfer.files[0];
                if (file) handleFile(file);
            });
        }

        // --- HANDLE FILE (shared by click and drop) ---
        function handleFile(file) {
            // Validate type
            if (!ALLOWED_TYPES.includes(file.type)) {
                alert('Please upload JPG, PNG, or GIF image');
                return;
            }
            
            // Validate size
            if (file.size > MAX_SIZE) {
                alert('File too large. Maximum 5MB.');
                return;
            }

            // Revoke old URL
            if (currentObjectURL) {
                URL.revokeObjectURL(currentObjectURL);
            }

            // Create preview URL
            currentObjectURL = URL.createObjectURL(file);
            
            // Show image in preview
            if (img) {
                img.src = currentObjectURL;
                img.onload = () => {
                    // Show image container, hide placeholder
                    if (imgContainer) imgContainer.classList.remove('hidden');
                    if (blurContainer) blurContainer.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                    if (circleMask) circleMask.classList.remove('hidden');
                    if (zoomContainer) zoomContainer.classList.remove('hidden');
                    
                    // Update blur background
                    if (blurImg) {
                        blurImg.src = currentObjectURL;
                        blurImg.classList.remove('hidden');
                    }
                    
                    // Center the image
                    const frameW = dropzone.offsetWidth;
                    const frameH = dropzone.offsetHeight;
                    const imgW = img.naturalWidth;
                    const imgH = img.naturalHeight;
                    
                    // Calculate cover scale
                    const coverScale = Math.max(frameW / imgW, frameH / imgH);
                    
                    img.style.width = imgW + 'px';
                    img.style.height = imgH + 'px';
                    img.style.transform = `translate(-50%, -50%) scale(${coverScale})`;
                    
                    // Reset zoom slider
                    if (zoomSlider) zoomSlider.value = 100;
                    
                    // Clear remove flag
                    if (removeInput) removeInput.value = '0';
                };
            }
        }

        // --- CHANGE BUTTON -> OPEN FILE PICKER ---
        if (changeBtn && fileInput) {
            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });
        }

        // --- RESET BUTTON -> RESET ZOOM ---
        if (resetBtn && zoomSlider) {
            resetBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                zoomSlider.value = 100;
                zoomSlider.dispatchEvent(new Event('input'));
            });
        }

        // --- DELETE BUTTON -> REMOVE IMAGE ---
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!confirm('Remove your profile photo?')) return;
                
                // Mark for removal
                if (removeInput) removeInput.value = '1';
                
                // Hide image elements
                if (imgContainer) imgContainer.classList.add('hidden');
                if (blurContainer) blurContainer.classList.add('hidden');
                if (circleMask) circleMask.classList.add('hidden');
                if (zoomContainer) zoomContainer.classList.add('hidden');
                
                // Show placeholder
                if (placeholder) placeholder.classList.remove('hidden');
                
                // Clear image
                if (img) img.src = '';
                if (blurImg) blurImg.src = '';
                
                // Clear file input
                if (fileInput) fileInput.value = '';
                
                // Clear cropped avatar
                if (croppedInput) croppedInput.value = '';
                
                // Revoke URL
                if (currentObjectURL) {
                    URL.revokeObjectURL(currentObjectURL);
                    currentObjectURL = null;
                }
            });
        }
    });
</script>