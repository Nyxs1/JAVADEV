{{-- Mentor Event Detail --}}
<div class="space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor']) }}"
        class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
        <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-4 h-4">
        <span>Back to Events</span>
    </a>

    {{-- Event Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-start gap-4">
            @if($mentorEvent->cover_image)
                <img src="{{ asset('storage/' . $mentorEvent->cover_image) }}" alt=""
                    class="w-24 h-18 rounded-lg object-cover">
            @endif
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900">{{ $mentorEvent->title }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $mentorEvent->start_at->format('d M Y, H:i') }} -
                    {{ $mentorEvent->end_at->format('H:i') }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $mentorEvent->isEnded() ? 'bg-slate-100 text-slate-600' : ($mentorEvent->isOngoing() ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $mentorEvent->status_label }}
                    </span>
                    @if($mentorRecord)
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                            {{ ucfirst($mentorRecord->role) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-200">
            <div class="text-center">
                <p class="text-2xl font-bold text-slate-900">{{ $participantCounts['registered'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Registered</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $participantCounts['present'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Present</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-red-600">{{ $participantCounts['absent'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Absent</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $participantCounts['completed'] ?? 0 }}</p>
                <p class="text-xs text-slate-500">Completed</p>
            </div>
        </div>
    </div>

    {{-- Subtabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="border-b border-slate-200">
            <nav class="flex gap-1 p-2">
                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'event' => $mentorEvent->slug, 'subtab' => 'participants']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($mentorSubtab ?? 'participants') === 'participants' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    Participants
                </a>
                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'event' => $mentorEvent->slug, 'subtab' => 'requirements']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($mentorSubtab ?? '') === 'requirements' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    Requirements
                </a>
                @if($mentorEvent->isEnded())
                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'event' => $mentorEvent->slug, 'subtab' => 'reviews']) }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($mentorSubtab ?? '') === 'reviews' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Reviews
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-6">
            @if(($mentorSubtab ?? 'participants') === 'participants')
                @include('pages.users.partials.events.shared.participants')
            @elseif(($mentorSubtab ?? '') === 'requirements')
                @include('pages.users.partials.events.mentor.requirements')
            @elseif(($mentorSubtab ?? '') === 'reviews')
                @include('pages.users.partials.events.mentor.reviews')
            @endif
        </div>
    </div>
</div>