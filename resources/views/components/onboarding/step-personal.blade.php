@props(['user'])

@php
    // Safe error handling dengan null-safe operator
    $errorsBag = session('errors');

    // border helper biar gak ada border-slate + border-red barengan
    $border = fn($field) => $errorsBag?->has($field) ? 'border-red-500' : 'border-slate-300';
@endphp

<div class="p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-2">Informasi Personal</h2>
        <p class="text-slate-600">Ceritain tentang diri kamu biar kita bisa kenal lebih dekat!</p>
    </div>

    {{-- PROFILE COVER (HORIZONTAL) - MIRRORS PROFILE DISPLAY EXACTLY --}}
    <div class="mb-8" id="profile-upload-section">
        {{-- Cover Frame (SAME as Profile) --}}
        <x-profile.photo-frame :user="$user" :src="null" :alt="'Profile Cover'"
            :fallback-initial="substr($user->username ?? 'U', 0, 1)" :show-editor="true" :has-image="false" />

        {{-- Hidden Inputs --}}
        <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/jpg,image/png,image/gif"
            class="hidden">
        <input type="hidden" name="cropped_avatar" id="cropped_avatar" value="">
        <input type="hidden" name="avatar_zoom" id="avatar_zoom_input" value="1">
        <input type="hidden" name="avatar_pan_x" id="avatar_pan_x_input" value="0">
        <input type="hidden" name="avatar_pan_y" id="avatar_pan_y_input" value="0">
        <canvas id="avatar-canvas" class="hidden" width="256" height="256"></canvas>

        {{-- Zoom Controls --}}
        <div id="avatar-zoom-container" class="hidden flex items-center justify-center gap-4 mt-4">
            <button type="button" id="avatar-change"
                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Change
            </button>
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                </svg>
                <input type="range" id="avatar-zoom" min="60" max="250" value="100"
                    class="w-32 h-1 bg-slate-200 rounded-full appearance-none cursor-pointer accent-blue-600">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                </svg>
            </div>
            <button type="button" id="avatar-reset"
                class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                Reset
            </button>
            <button type="button" id="avatar-delete" class="group inline-flex items-center gap-2 px-4 py-2.5 
                       bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 
                       text-red-600 rounded-xl text-sm font-medium 
                       hover:from-red-500 hover:to-pink-500 hover:text-white hover:border-transparent
                       hover:shadow-lg hover:shadow-red-500/25
                       transition-all duration-300 ease-out">
                <svg class="w-4 h-4 transition-transform group-hover:scale-110 group-hover:rotate-12" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span class="group-hover:tracking-wide transition-all">Hapus</span>
            </button>
        </div>

        {{-- Hints --}}
        <p id="avatar-hint" class="text-xs text-slate-500 mt-3 text-center">Ini opsional kok, tapi bikin profilmu lebih
            hidup</p>
        <p class="text-xs text-slate-400 text-center">JPG, PNG, GIF • Max 5MB</p>
        <p id="avatar-error" class="text-xs text-red-500 mt-1 text-center hidden"></p>
    </div>

    {{-- Nama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label for="first_name" class="block text-sm font-medium text-slate-700 mb-1">
                Nama Depan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="first_name" id="first_name"
                value="{{ old('first_name', $user->first_name ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $border('first_name') }}"
                placeholder="John" required>
            @if ($errorsBag?->has('first_name'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('first_name') }}</p>
            @endif
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium text-slate-700 mb-1">
                Nama Belakang <span class="text-red-500">*</span>
            </label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $border('last_name') }}"
                placeholder="Doe" required>
            @if ($errorsBag?->has('last_name'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('last_name') }}</p>
            @endif
        </div>
    </div>

    {{-- Nama Tengah (Optional) --}}
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <input type="checkbox" id="show-middle-name" class="mr-2">
            <label for="show-middle-name" class="text-sm text-slate-600">Punya nama tengah?</label>
        </div>

        <div id="middle-name-field" class="hidden">
            <label for="middle_name" class="block text-sm font-medium text-slate-700 mb-1">
                Nama Tengah
            </label>
            <input type="text" name="middle_name" id="middle_name"
                value="{{ old('middle_name', $user->middle_name ?? '') }}"
                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $border('middle_name') }}"
                placeholder="William">
            @if ($errorsBag?->has('middle_name'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('middle_name') }}</p>
            @endif
        </div>
    </div>

    {{-- Tanggal Lahir --}}
    <div class="mb-6">
        <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-1">
            Tanggal Lahir <span class="text-red-500">*</span>
        </label>

        {{-- Date Input with Modal Trigger --}}
        <div class="relative">
            <input type="text" id="date-display"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white pr-12"
                placeholder="DD/MM/YYYY atau klik kalender">

            <button type="button" id="open-date-modal"
                class="absolute inset-y-0 right-3 flex items-center justify-center text-slate-400 hover:text-blue-500"
                aria-label="Buka kalender">
                <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="" class="w-5 h-5">
            </button>

            {{-- Hidden actual input --}}
            <input type="date" name="birth_date" id="birth_date"
                value="{{ old('birth_date', isset($user->birth_date) ? \Illuminate\Support\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}"
                class="hidden" required>
        </div>

        {{-- Error message (inline, below input) --}}
        <p id="birth-date-error" class="text-red-600 text-sm mt-1 hidden"></p>

        @if ($errorsBag?->has('birth_date'))
            <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('birth_date') }}</p>
        @endif

        <div id="age-display" class="text-sm text-slate-600 mt-1 hidden">
            Umur: <span id="age-text">-</span> tahun
        </div>

        {{-- Modal Date Picker --}}
        <div id="date-modal"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 border border-slate-200">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 flex items-center">
                            <img src="{{ asset('assets/icons/calendar-icon.svg') }}" alt="Calendar"
                                class="w-5 h-5 mr-2">
                            Pilih Tanggal Lahir
                        </h3>
                        <button type="button" id="close-modal" class="text-slate-400 hover:text-slate-600">
                            <img src="{{ asset('assets/icons/close-icon.svg') }}" alt="Close" class="w-5 h-5">
                        </button>
                    </div>
                </div>

                {{-- Date Selection --}}
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        {{-- Year --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tahun</label>
                            <select id="year-select"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <!-- Populated by JS -->
                            </select>
                        </div>

                        {{-- Month --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Bulan</label>
                            <select id="month-select"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">--</option>
                                <option value="01">Jan</option>
                                <option value="02">Feb</option>
                                <option value="03">Mar</option>
                                <option value="04">Apr</option>
                                <option value="05">Mei</option>
                                <option value="06">Jun</option>
                                <option value="07">Jul</option>
                                <option value="08">Agu</option>
                                <option value="09">Sep</option>
                                <option value="10">Okt</option>
                                <option value="11">Nov</option>
                                <option value="12">Des</option>
                            </select>
                        </div>

                        {{-- Day --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal</label>
                            <select id="day-select"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <!-- Populated by JS -->
                            </select>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex space-x-3">
                        <button type="button" id="cancel-modal"
                            class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="button" id="confirm-modal"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Pilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
        <div class="flex items-start">
            <img src="{{ asset('assets/icons/info-icon.svg') }}" alt="Info"
                class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0 text-slate-600">
            <div>
                <h4 class="text-sm font-medium text-slate-900 mb-1">
                    Kenapa kita perlu ini?
                </h4>
                <p class="text-sm text-slate-700">
                    Biar kita bisa kasih pengalaman belajar yang super personal dan rekomendasi mentor/project yang
                    cocok banget sama kamu! Semakin lengkap, semakin asyik journey-nya!
                </p>
            </div>
        </div>
    </div>
</div>