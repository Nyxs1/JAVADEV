{{-- Mentor Events List --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    {{-- Status Filters --}}
    <div class="p-4 border-b border-slate-200">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ !$mentorStatus ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                All ({{ $mentorCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'status' => 'upcoming']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $mentorStatus === 'upcoming' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Upcoming ({{ $mentorCounts['upcoming'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'status' => 'ongoing']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $mentorStatus === 'ongoing' ? 'bg-green-100 text-green-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Ongoing ({{ $mentorCounts['ongoing'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'status' => 'ended']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ $mentorStatus === 'ended' ? 'bg-slate-200 text-slate-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Ended ({{ $mentorCounts['ended'] ?? 0 }})
            </a>
        </div>
    </div>

    {{-- Events Table --}}
    @if($mentorEvents->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Participants</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($mentorEvents as $event)
                        @php
                            $mentorPivot = $event->mentors->first()?->pivot;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($event->cover_image)
                                        <img src="{{ asset('storage/' . $event->cover_image) }}" alt=""
                                            class="w-12 h-9 rounded object-cover">
                                    @else
                                        <div class="w-12 h-9 rounded bg-slate-200 flex items-center justify-center">
                                            <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-5 h-5 opacity-40">
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $event->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $event->start_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                                    {{ ucfirst($mentorPivot?->role ?? 'mentor') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm">
                                    <span class="text-slate-900 font-medium">{{ $event->registered_count ?? 0 }}</span>
                                    <span class="text-slate-400">/</span>
                                    <span class="text-green-600">{{ $event->present_count ?? 0 }}</span>
                                    <span class="text-slate-400">/</span>
                                    <span class="text-blue-600">{{ $event->completed_count ?? 0 }}</span>
                                </div>
                                <p class="text-xs text-slate-500">Reg / Present / Done</p>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full
                                                            {{ $event->isEnded() ? 'bg-slate-100 text-slate-600' : ($event->isOngoing() ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $event->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor', 'event' => $event->slug]) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    <span>Manage</span>
                                    <img src="{{ asset('assets/icons/arrow-right.svg') }}" alt="" class="w-4 h-4">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($mentorEvents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $mentorEvents->appends(['tab' => 'mentor', 'status' => $mentorStatus])->links() }}
            </div>
        @endif
    @else
        <div class="p-8 text-center">
            <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
            <p class="text-slate-500">Belum ada event yang di-mentor</p>
        </div>
    @endif
</div>