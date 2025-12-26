@props(['events', 'type' => 'member', 'user', 'isOwnProfile' => false])

@php
    $statusLabels = [
        'completed' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-700'],
        'attended' => ['label' => 'Attended', 'class' => 'bg-blue-100 text-blue-700'],
        'absent' => ['label' => 'Absent', 'class' => 'bg-red-100 text-red-700'],
        'registered' => ['label' => 'Registered', 'class' => 'bg-yellow-100 text-yellow-700'],
    ];

    $roleClasses = [
        'mentor' => 'bg-blue-100 text-blue-700',
        'co-mentor' => 'bg-indigo-100 text-indigo-700',
        'speaker' => 'bg-amber-100 text-amber-700',
        'moderator' => 'bg-emerald-100 text-emerald-700',
    ];

    $goalStatusLabels = [
        'planned' => ['label' => 'Planned', 'class' => 'text-slate-500'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'text-blue-600'],
        'achieved' => ['label' => 'Achieved', 'class' => 'text-green-600'],
        'done' => ['label' => 'Done', 'class' => 'text-green-600'],
    ];
@endphp

<div class="event-history">
    @if(count($events) > 0)
        <div class="space-y-3">
            @foreach($events as $event)
                <div class="event-history-item p-4 border border-slate-200 rounded-lg hover:border-slate-300 transition-colors">
                    <div class="flex items-start gap-4">
                        {{-- Thumbnail --}}
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 shrink-0">
                            @if(!empty($event['cover_image']))
                                <img src="{{ asset('storage/' . $event['cover_image']) }}" 
                                        alt="" 
                                        class="w-full h-full object-cover"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 hidden">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6 opacity-50 invert">
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-6 h-6 opacity-50 invert">
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-slate-900 truncate">{{ $event['title'] }}</h4>
                            
                            <div class="flex items-center gap-3 mt-2 flex-wrap">
                                <span class="flex items-center gap-1 text-xs text-slate-500">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-3 h-3 opacity-60">
                                    {{ $event['date'] }}
                                </span>

                                @if($type === 'member')
                                    @php $statusInfo = $statusLabels[$event['status']] ?? $statusLabels['registered']; @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                @else
                                    @php $roleClass = $roleClasses[$event['role']] ?? 'bg-slate-100 text-slate-700'; @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleClass }}">
                                        {{ $event['role_label'] }}
                                    </span>
                                @endif
                            </div>

                            @if($type === 'mentor')
                                <div class="flex items-center gap-4 mt-2 text-xs text-slate-500">
                                    <span>{{ $event['achieved_participants'] }} participants</span>
                                    @php $goalInfo = $goalStatusLabels[$event['goal_status']] ?? $goalStatusLabels['planned']; @endphp
                                    <span class="{{ $goalInfo['class'] }}">{{ $goalInfo['label'] }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($type === 'member' && $event['status'] === 'completed' && $event['certificate_url'])
                                <a href="{{ $event['certificate_url'] }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                                    <img src="{{ asset('assets/icons/download.svg') }}" alt="" class="w-3 h-3">
                                    Certificate
                                </a>
                            @endif
                            <a href="{{ route('events.show', $event['slug']) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-8 h-8 opacity-50">
            </div>
            
            @if($type === 'member')
                <h3 class="text-lg font-medium text-slate-900 mb-2">No Events Yet</h3>
                <p class="text-slate-600 mb-4 max-w-sm mx-auto">
                    {{ $isOwnProfile ? 'You have not joined any events yet. Check out upcoming events!' : 'This user has not joined any events yet.' }}
                </p>
                @if($isOwnProfile)
                    <a href="{{ route('events.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors inline-block">
                        Browse Events
                    </a>
                @endif
            @else
                <h3 class="text-lg font-medium text-slate-900 mb-2">No Mentoring History</h3>
                <p class="text-slate-600 max-w-sm mx-auto">
                    {{ $isOwnProfile ? 'You have not mentored any events yet.' : 'This user has not mentored any events yet.' }}
                </p>
            @endif
        </div>
    @endif
</div>
