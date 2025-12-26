@props(['user'])

@php
    $errorsBag = session('errors');
    $border = fn($field) =>
        ($errorsBag?->has($field))
        ? 'border-red-500'
        : 'border-slate-300';
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Ubah Password</h3>
        <p class="text-sm text-slate-600 mt-1">Biar akun kamu tetap aman.</p>
    </div>

    <form method="POST" action="{{ route('profile.change-password') }}" class="p-6" id="password-form">
        @csrf

        {{-- Current Password --}}
        <div class="mb-4">
            <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">
                Password Saat Ini <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <input type="password" name="current_password" id="current_password"
                    class="w-full px-3 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $border('current_password') }}"
                    required>

                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle"
                    data-target="current_password" aria-label="Toggle password visibility">
                    <img src="{{ asset('assets/icons/eye.svg') }}" alt="" class="w-5 h-5 opacity-50 hover:opacity-80">
                </button>
            </div>

            @if ($errorsBag?->has('current_password'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('current_password') }}</p>
            @endif
        </div>

        {{-- New Password --}}
        <div class="mb-4">
            <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1">
                Password Baru <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <input type="password" name="new_password" id="new_password"
                    class="w-full px-3 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $border('new_password') }}"
                    required>

                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle"
                    data-target="new_password" aria-label="Toggle password visibility">
                    <img src="{{ asset('assets/icons/eye.svg') }}" alt="" class="w-5 h-5 opacity-50 hover:opacity-80">
                </button>
            </div>

            @if ($errorsBag?->has('new_password'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('new_password') }}</p>
            @endif

            <p class="text-xs text-slate-500 mt-1">Minimal 8 karakter.</p>
        </div>

        {{-- Confirm --}}
        <div class="mb-6">
            <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">
                Konfirmasi Password Baru <span class="text-red-500">*</span>
            </label>

            <div class="relative">
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                    class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>

                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle"
                    data-target="new_password_confirmation" aria-label="Toggle password visibility">
                    <img src="{{ asset('assets/icons/eye.svg') }}" alt="" class="w-5 h-5 opacity-50 hover:opacity-80">
                </button>
            </div>

            @if ($errorsBag?->has('new_password_confirmation'))
                <p class="text-red-600 text-sm mt-1">{{ $errorsBag->first('new_password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <button type="reset"
                class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Simpan
            </button>
        </div>
    </form>
</div>