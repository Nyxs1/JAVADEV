<div class="p-6 border-b border-slate-200">
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-2">Fokus Belajar</h2>
        <p class="text-slate-600">Pilih area yang ingin Anda pelajari (opsional)</p>
    </div>

    {{-- Focus Areas Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
        @php
            $focusAreas = [
                'web-development' => ['label' => 'Web Development', 'icon' => '🌐'],
                'mobile-development' => ['label' => 'Mobile Development', 'icon' => '📱'],
                'data-science' => ['label' => 'Data Science', 'icon' => '📊'],
                'machine-learning' => ['label' => 'Machine Learning', 'icon' => '🤖'],
                'ui-ux-design' => ['label' => 'UI/UX Design', 'icon' => '🎨'],
                'devops' => ['label' => 'DevOps', 'icon' => '⚙️'],
                'cybersecurity' => ['label' => 'Cybersecurity', 'icon' => '🔒'],
                'game-development' => ['label' => 'Game Development', 'icon' => '🎮'],
                'blockchain' => ['label' => 'Blockchain', 'icon' => '⛓️'],
            ];
        @endphp

        @php
            $oldFocusAreas = old('focus_areas', []);
            if (is_string($oldFocusAreas)) {
                $oldFocusAreas = json_decode($oldFocusAreas, true) ?? [];
            }
        @endphp

        @foreach ($focusAreas as $key => $area)
            <label class="focus-area-card cursor-pointer block">
                <input type="checkbox" name="focus_areas[]" value="{{ $key }}" class="hidden focus-area-input"
                    {{ in_array($key, $oldFocusAreas) ? 'checked' : '' }}>

                <div
                    class="focus-area-content relative p-4 border-2 border-slate-200 rounded-lg transition hover:border-blue-300">
                    {{-- Badge selected --}}
                    <span
                        class="selected-badge hidden absolute top-2 right-2 text-xs font-semibold px-2 py-1 rounded-full bg-blue-600 text-white">
                        ✓ Dipilih
                    </span>

                    <div class="text-center">
                        <div class="text-2xl mb-2">{{ $area['icon'] }}</div>
                        <div class="text-sm font-medium text-slate-700">{{ $area['label'] }}</div>
                    </div>
                </div>
            </label>
        @endforeach

    </div>

    {{-- Selected Count --}}
    <p class="text-sm text-slate-600">
        <span id="selected-focus-count">0</span> area dipilih
    </p>
</div>
