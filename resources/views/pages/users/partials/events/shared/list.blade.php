{{-- Admin Events List --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    {{-- Status Filters --}}
    <div class="p-4 border-b border-slate-200">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ !request('status') ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                All ({{ $adminCounts['all'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'status' => 'draft']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'draft' ? 'bg-slate-200 text-slate-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Draft ({{ $adminCounts['draft'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'status' => 'upcoming']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'upcoming' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Upcoming ({{ $adminCounts['upcoming'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'status' => 'ongoing']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'ongoing' ? 'bg-green-100 text-green-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Ongoing ({{ $adminCounts['ongoing'] ?? 0 }})
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'status' => 'ended']) }}"
                class="px-3 py-1.5 text-sm rounded-lg transition-colors {{ request('status') === 'ended' ? 'bg-slate-200 text-slate-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Ended ({{ $adminCounts['ended'] ?? 0 }})
            </a>
        </div>
    </div>

    {{-- Events Table --}}
    @if($adminEvents->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Participants</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Mentors</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($adminEvents as $event)
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
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                    {{ ucfirst($event->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-medium text-slate-900">{{ $event->registered_count ?? 0 }}</span>
                                @if($event->capacity)
                                    <span class="text-slate-400">/{{ $event->capacity }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-sm font-medium text-slate-900">{{ $event->mentors_count ?? 0 }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                            {{ $event->status === 'draft' ? 'bg-slate-100 text-slate-600' : '' }}
                                                            {{ $event->isEnded() ? 'bg-slate-100 text-slate-600' : '' }}
                                                            {{ $event->isOngoing() ? 'bg-green-100 text-green-700' : '' }}
                                                            {{ $event->isUpcoming() ? 'bg-blue-100 text-blue-700' : '' }}">
                                    {{ $event->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $event->slug, 'subtab' => 'edit']) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <img src="{{ asset('assets/icons/edit.svg') }}" alt="" class="w-4 h-4">
                                    </a>
                                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $event->slug, 'subtab' => 'mentors']) }}"
                                        class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                        title="Mentors">
                                        <img src="{{ asset('assets/icons/users.svg') }}" alt="" class="w-4 h-4">
                                    </a>
                                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $event->slug, 'subtab' => 'requirements']) }}"
                                        class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                        title="Requirements">
                                        <img src="{{ asset('assets/icons/clipboard.svg') }}" alt="" class="w-4 h-4">
                                    </a>
                                    @if($event->isEnded())
                                        <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $event->slug, 'subtab' => 'reviews']) }}"
                                            class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Reviews">
                                            <img src="{{ asset('assets/icons/star.svg') }}" alt="" class="w-4 h-4">
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($adminEvents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $adminEvents->appends(['tab' => 'admin', 'status' => request('status')])->links() }}
            </div>
        @endif
    @else
        <div class="p-8 text-center">
            <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
            <p class="text-slate-500">Belum ada event</p>
        </div>
    @endif
</div>