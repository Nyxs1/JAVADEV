{{-- Overview Tab --}}
<div class="space-y-6">
    {{-- Welcome Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ $user->username }}</h1>
        <p class="text-slate-600 mt-1">Here's your activity summary</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6">
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $eventStats['registered'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Events Registered</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="w-6 h-6">
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $eventStats['attended'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Events Attended</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <img src="{{ asset('assets/icons/award.svg') }}" alt="" class="w-6 h-6">
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $eventStats['completed'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Events Completed</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mentor Stats (if mentor) --}}
    @if($isMentor && isset($mentorStats))
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Mentor Activity</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-2xl font-bold text-slate-900">{{ $mentorStats['events'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Total Events Mentored</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-2xl font-bold text-slate-900">{{ $mentorStats['upcoming'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Upcoming Events</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Upcoming Events --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Upcoming Events</h2>

        @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingEvents as $event)
                    <a href="{{ route('events.show', $event->slug) }}"
                        class="flex items-center gap-4 p-3 rounded-lg hover:bg-slate-50 transition-colors">
                        @if($event->cover_image)
                            <img src="{{ asset("storage/{$event->cover_image}") }}" alt=""
                                class="w-16 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-12 rounded-lg bg-slate-200 flex items-center justify-center">
                                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6 opacity-40">
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-900 truncate">{{ $event->title }}</p>
                            <p class="text-sm text-slate-500">{{ $event->start_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                            Registered
                        </span>
                    </a>
                @endforeach
            </div>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'events']) }}"
                class="inline-flex items-center gap-2 mt-4 text-sm text-blue-600 hover:text-blue-700">
                <span>View All Events</span>
                <img src="{{ asset('assets/icons/arrow-right.svg') }}" alt="" class="w-4 h-4">
            </a>
        @else
            <div class="text-center py-8">
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
                <p class="text-slate-500">Tidak ada event yang akan datang</p>
                <a href="{{ route('events.index') }}" class="inline-block mt-3 text-sm text-blue-600 hover:text-blue-700">
                    Jelajahi Event
                </a>
            </div>
        @endif
    </div>
</div>