<div class="p-6">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-2">Pilih Role Anda</h2>
        <p class="text-slate-600">
            Mau jadi apa nih di komunitas Java Developer Group? Pilih yang paling cocok sama goals kamu!
        </p>
    </div>

    {{-- Role Options --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 items-stretch">
        {{-- Member Role --}}
        <label class="role-card cursor-pointer h-full">
            <input type="radio" name="preferred_role" value="member" class="hidden role-input"
                {{ old('preferred_role', 'member') === 'member' ? 'checked' : '' }} required>

            <div
                class="p-6 border-2 border-slate-200 rounded-lg hover:border-blue-300 transition-colors role-content h-full flex flex-col">
                <div class="flex items-start flex-1">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 shrink-0">
                        <img src="{{ asset('assets/icons/user.svg') }}" alt="User" class="w-6 h-6 text-blue-600">
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Member</h3>
                        <p class="text-sm text-slate-600 mb-3 flex items-center">
                            Yuk mulai journey belajar kamu! Akses semua materi, ikut diskusi seru, dan berkembang bareng
                            teman-teman developer lainnya!
                            <img src="{{ asset('assets/icons/rocket-icon.svg') }}" alt="Rocket" class="w-4 h-4 ml-2">
                        </p>

                        <ul class="text-sm text-slate-600 space-y-1">
                            @foreach (['Akses ke semua course', 'Bergabung dalam diskusi', 'Ikut challenge & event'] as $item)
                                <li class="flex items-center">
                                    <img src="{{ asset('assets/icons/check.svg') }}" alt="Check"
                                        class="w-4 h-4 text-green-500 mr-2 shrink-0">
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </label>

        {{-- Mentor Role --}}
        <label class="role-card cursor-pointer h-full">
            <input type="radio" name="preferred_role" value="mentor" class="hidden role-input"
                {{ old('preferred_role') === 'mentor' ? 'checked' : '' }}>

            <div
                class="p-6 border-2 border-slate-200 rounded-lg hover:border-blue-300 transition-colors role-content h-full flex flex-col">
                <div class="flex items-start flex-1">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 shrink-0">
                        <img src="{{ asset('assets/icons/book.svg') }}" alt="Book" class="w-6 h-6 text-green-600">
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Mentor</h3>
                        <p class="text-sm text-slate-600 mb-3">
                            Siap jadi hero buat junior developer? Share ilmu, bimbing mereka, dan bantu komunitas makin
                            keren!
                        </p>

                        <ul class="text-sm text-slate-600 space-y-1">
                            @foreach (['Semua akses member', 'Buat & kelola course', 'Moderasi diskusi'] as $item)
                                <li class="flex items-center">
                                    <img src="{{ asset('assets/icons/check.svg') }}" alt="Check"
                                        class="w-4 h-4 text-green-500 mr-2 shrink-0">
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </label>
    </div>

    @error('preferred_role')
        <p class="text-red-600 text-sm mb-4">{{ $message }}</p>
    @enderror

    {{-- Mentor Notice --}}
    <div id="mentor-notice"
        class="bg-amber-50 border border-amber-200 rounded-lg p-4 {{ old('preferred_role') === 'mentor' ? '' : 'hidden' }}">
        <div class="flex items-start">
            <img src="{{ asset('assets/icons/warning.svg') }}" alt="Warning"
                class="w-5 h-5 text-amber-600 mt-0.5 mr-3 shrink-0">

            <div>
                <h4 class="text-sm font-medium text-amber-900 mb-1">Permohonan Mentor</h4>
                <p class="text-sm text-amber-800">
                    Permintaan untuk menjadi mentor akan direview oleh admin.
                    Anda akan tetap sebagai member hingga permohonan disetujui.
                </p>
            </div>
        </div>
    </div>
</div>
