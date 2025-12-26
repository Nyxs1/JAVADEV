{{-- Events Tab (Dashboard) --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h1 class="text-2xl font-bold text-slate-900">My Events</h1>
        <p class="text-slate-600 mt-1">Track your event participation and history</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6">
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ $eventStats['registered'] ?? 0 }}</p>
                    <p class="text-sm text-slate-600">Registered</p>
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
                    <p class="text-sm text-slate-600">Attended</p>
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
                    <p class="text-sm text-slate-600">Completed</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Event List with Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        {{-- Filter Tabs --}}
        <div class="border-b border-slate-200">
            <nav class="flex px-4" aria-label="Event filters">
                @php
                    $currentFilter = $eventFilter ?? 'upcoming';
                    $filters = [
                        'upcoming' => ['label' => 'Upcoming', 'count' => $eventCounts['upcoming'] ?? 0],
                        'ongoing' => ['label' => 'Ongoing', 'count' => $eventCounts['ongoing'] ?? 0],
                        'past' => ['label' => 'Past', 'count' => $eventCounts['past'] ?? 0],
                    ];
                @endphp
                @foreach($filters as $key => $filter)
                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'events', 'filter' => $key]) }}"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $currentFilter === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-600 hover:text-blue-600 hover:border-blue-300' }}">
                        {{ $filter['label'] }}
                        @if($filter['count'] > 0)
                            <span
                                class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full {{ $currentFilter === $key ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $filter['count'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Event List --}}
        @if(isset($userEvents) && $userEvents->count() > 0)
            <div class="divide-y divide-slate-200">
                @foreach($userEvents as $event)
                    @php
                        $pivot = $event->pivot;
                        $isUpcoming = $event->isUpcoming();
                        $isOngoing = $event->isOngoing();
                        $isEnded = $event->isEnded();

                        // Determine status badge
                        if ($isUpcoming) {
                            $statusLabel = 'Registered';
                            $statusClass = 'bg-blue-100 text-blue-700';
                        } elseif ($isOngoing) {
                            $statusLabel = 'In Progress';
                            $statusClass = 'bg-green-100 text-green-700';
                        } else {
                            // Past event - check attendance/completion
                            if ($pivot->completion_status === 'completed') {
                                $statusLabel = 'Completed';
                                $statusClass = 'bg-purple-100 text-purple-700';
                            } elseif ($pivot->attendance_status === 'present') {
                                $statusLabel = 'Attended';
                                $statusClass = 'bg-green-100 text-green-700';
                            } elseif ($pivot->attendance_status === 'absent') {
                                $statusLabel = 'Missed';
                                $statusClass = 'bg-red-100 text-red-700';
                            } else {
                                $statusLabel = 'Ended';
                                $statusClass = 'bg-slate-100 text-slate-600';
                            }
                        }
                    @endphp
                    <div class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            @if($event->cover_image)
                                <img src="{{ asset('storage/' . $event->cover_image) }}" alt=""
                                    class="w-16 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-12 rounded-lg bg-slate-200 flex items-center justify-center">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6 opacity-40">
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('events.show', $event->slug) }}"
                                    class="font-medium text-slate-900 hover:text-blue-600 truncate block">
                                    {{ $event->title }}
                                </a>
                                <p class="text-sm text-slate-500">{{ $event->start_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                            <a href="{{ route('events.show', $event->slug) }}"
                                class="px-3 py-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($userEvents->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $userEvents->appends(['tab' => 'events', 'filter' => $currentFilter])->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center">
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
                @if($currentFilter === 'upcoming')
                    <p class="text-slate-500">Tidak ada event yang akan datang</p>
                @elseif($currentFilter === 'ongoing')
                    <p class="text-slate-500">Tidak ada event yang sedang berlangsung</p>
                @else
                    <p class="text-slate-500">Belum ada riwayat event</p>
                @endif
                <a href="{{ route('events.index') }}" class="inline-block mt-3 text-sm text-blue-600 hover:text-blue-700">
                    Jelajahi Event
                </a>
            </div>
        @endif
    </div>
</div>