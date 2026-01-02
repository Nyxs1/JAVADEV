@props(['skill'])

@php
    use App\Http\Support\Enums\SkillLevel;

    $level = SkillLevel::fromValue($skill->level);
    $levelLabel = $level->label();
    $levelPercent = $level->percent();
    $levelGradient = $level->gradient();
@endphp

<div
    class="group relative p-4 bg-slate-50 hover:bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
    {{-- Hover Tooltip --}}
    <div
        class="absolute -top-10 left-4 px-3 py-1.5 bg-slate-900 text-white text-xs font-medium rounded-lg 
                opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 
                transition-all duration-200 ease-out pointer-events-none z-20 whitespace-nowrap
                before:content-[''] before:absolute before:top-full before:left-4 before:border-4 before:border-transparent before:border-t-slate-900">
        Level: {{ $levelLabel }}
    </div>

    <div class="flex items-center gap-4">
        <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-slate-800">{{ $skill->tech_name }}</span>
                <span class="text-xs font-medium text-slate-400">{{ $levelPercent }}%</span>
            </div>
            <div class="h-2.5 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $levelGradient }}"
                    style="width: {{ $levelPercent }}%"></div>
            </div>
        </div>

        {{-- Level Dropdown --}}
        <form method="POST" action="{{ route('profile.skills.update', $skill->id) }}" class="shrink-0">
            @csrf
            @method('PUT')
            <x-forms.skill-level-select name="level" :selected="$skill->level" onchange="this.form.submit()"
                class="px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white cursor-pointer hover:border-blue-300 transition-colors" />
        </form>

        {{-- Delete Button --}}
        <form method="POST" action="{{ route('profile.skills.destroy', $skill->id) }}" class="shrink-0">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </form>
    </div>
</div>