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
                <canvas id="avatar-canvas" class="hidden" width="768" height="256"></canvas>
            </div>

            {{-- ======================================= --}}
            {{-- PHOTO CONTROLS (Gen Z Style) --}}
            {{-- ======================================= --}}
            <div id="avatar-zoom-container" class="flex flex-wrap items-center justify-center gap-3 {{ $hasAvatar ? '' : 'hidden' }}">
                {{-- Change Button --}}
                <button type="button" id="avatar-change" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 
                           rounded-xl text-sm font-medium hover:bg-slate-50 hover:border-slate-300 
                           transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Ganti</span>
                </button>

                {{-- Zoom Slider (Glassmorphism style) --}}
                <div class="flex items-center gap-2 px-4 py-2.5 bg-white/80 backdrop-blur-sm border border-slate-200 rounded-xl shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                    </svg>
                    <input type="range" id="avatar-zoom" min="60" max="200" value="100" 
                        class="w-28 h-1.5 bg-gradient-to-r from-slate-200 to-slate-300 rounded-full appearance-none cursor-pointer
                               [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 
                               [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-gradient-to-br 
                               [&::-webkit-slider-thumb]:from-blue-500 [&::-webkit-slider-thumb]:to-indigo-600 
                               [&::-webkit-slider-thumb]:shadow-md [&::-webkit-slider-thumb]:cursor-pointer
                               [&::-webkit-slider-thumb]:transition-transform [&::-webkit-slider-thumb]:hover:scale-110">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                    </svg>
                </div>

                {{-- Reset Button --}}
                <button type="button" id="avatar-reset" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-600 
                           rounded-xl text-sm font-medium hover:bg-slate-200 
                           transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </button>

                {{-- Remove Button (Gen Z Style - Pill with gradient hover) --}}
                <button type="button" id="avatar-delete" 
                    class="group inline-flex items-center gap-2 px-4 py-2.5 
                           bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 
                           text-red-600 rounded-xl text-sm font-medium 
                           hover:from-red-500 hover:to-pink-500 hover:text-white hover:border-transparent
                           hover:shadow-lg hover:shadow-red-500/25
                           transition-all duration-300 ease-out">
                    <svg class="w-4 h-4 transition-transform group-hover:scale-110 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span class="group-hover:tracking-wide transition-all">Hapus</span>
                </button>
            </div>
            
            {{-- Error Display --}}
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
                <button type="submit" id="save-profile-btn" 
                    class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl 
                           hover:from-blue-700 hover:to-indigo-700 transition-all font-medium 
                           shadow-md hover:shadow-lg hover:shadow-blue-500/25">
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
    });
</script>