{{-- Admin Finalization Panel --}}
<div class="space-y-6">
    {{-- Pending Events --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Pending Finalization</h2>
            @if(isset($pendingEvents) && $pendingEvents->count() > 0)
                <form action="{{ route('users.finalization.batch') }}" method="POST" class="inline"
                    onsubmit="return confirm('Finalize all pending events?')">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Finalize All
                    </button>
                </form>
            @endif
        </div>

        @if(isset($pendingEvents) && $pendingEvents->count() > 0)
            <div class="divide-y divide-slate-200">
                @foreach($pendingEvents as $event)
                    <div class="p-4 flex items-center justify-between">
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
                                <p class="text-xs text-slate-500">Ended {{ $event->end_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-sm text-right">
                                <p class="text-slate-900">{{ $event->registered_count ?? 0 }} registered</p>
                                <p class="text-slate-500">{{ $event->present_count ?? 0 }} present /
                                    {{ $event->completed_count ?? 0 }} completed</p>
                            </div>
                            <form action="{{ route('users.events.finalize', $event->slug) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    Finalize
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center">
                <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
                <p class="text-slate-500">Tidak ada event yang perlu difinalisasi</p>
            </div>
        @endif
    </div>

    {{-- Recently Finalized --}}
    @if(isset($finalizedEvents) && $finalizedEvents->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="p-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">Recently Finalized</h2>
            </div>
            <div class="divide-y divide-slate-200">
                @foreach($finalizedEvents as $event)
                    <div class="p-4 flex items-center justify-between">
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
                                <p class="text-xs text-slate-500">Finalized {{ $event->finalized_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-right">
                            <p class="text-slate-900">{{ $event->registered_count ?? 0 }} registered</p>
                            <p class="text-green-600">{{ $event->completed_count ?? 0 }} completed</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>