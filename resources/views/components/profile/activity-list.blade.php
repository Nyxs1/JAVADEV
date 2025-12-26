@props(['activities', 'type', 'user', 'isOwnProfile' => false])

@php
    $emptyStates = [
        'portfolio' => [
            'title' => 'No Portfolio Yet',
            'desc_own' => 'You have not created any portfolio projects. Start building your portfolio to showcase your skills!',
            'desc_other' => 'This user has not created any portfolio projects yet.',
            'action' => 'Create Portfolio',
        ],
        'course' => [
            'title' => 'No Courses Yet',
            'desc_own' => 'You have not enrolled in any courses. Check out the course catalog and start learning!',
            'desc_other' => 'This user has not enrolled in any courses yet.',
            'action' => 'Browse Courses',
        ],
        'discussion' => [
            'title' => 'No Discussions Yet',
            'desc_own' => 'You have not participated in any discussions. Join the community and start engaging!',
            'desc_other' => 'This user has not participated in any discussions yet.',
            'action' => 'Join Discussion',
        ],
        'challenge' => [
            'title' => 'No Challenges Yet',
            'desc_own' => 'You have not completed any challenges. Test your skills with coding challenges!',
            'desc_other' => 'This user has not completed any challenges yet.',
            'action' => 'Take Challenge',
        ],
    ];
    
    $state = $emptyStates[$type] ?? $emptyStates['portfolio'];
@endphp

<div class="activity-list">
    @if(count($activities) > 0)
        <div class="space-y-4">
            @foreach($activities as $activity)
                <div class="activity-item p-4 border border-slate-200 rounded-lg hover:border-slate-300 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-slate-900">{{ $activity['title'] }}</h4>
                            
                            @if(isset($activity['description']))
                                <p class="text-sm text-slate-600 mt-1">{{ $activity['description'] }}</p>
                            @endif

                            <div class="flex items-center gap-4 mt-3 text-xs text-slate-500">
                                @if(isset($activity['date']))
                                    <span class="flex items-center gap-1">
                                        <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-3 h-3 shrink-0 opacity-60" onerror="this.style.display='none'">
                                        {{ $activity['date'] }}
                                    </span>
                                @endif

                                @if(isset($activity['status']))
                                    <span class="flex items-center gap-1">
                                        <div class="w-2 h-2 rounded-full {{ $activity['status'] === 'completed' ? 'bg-green-500' : ($activity['status'] === 'in-progress' ? 'bg-yellow-500' : 'bg-slate-400') }}"></div>
                                        {{ ucfirst(str_replace('-', ' ', $activity['status'])) }}
                                    </span>
                                @endif

                                @if(isset($activity['progress']))
                                    <span class="flex items-center gap-1">
                                        {{ $activity['progress'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if(isset($activity['image']) && $activity['image'])
                            <div class="ml-4 shrink-0">
                                <img src="{{ $activity['image'] }}" alt="{{ $activity['title'] }}" 
                                     class="w-16 h-16 rounded-lg object-cover"
                                     onerror="this.style.display='none'">
                            </div>
                        @endif
                    </div>

                    @if(isset($activity['tags']) && count($activity['tags']) > 0)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($activity['tags'] as $tag)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Load More Button --}}
        @if(count($activities) >= 5)
            <div class="text-center mt-6">
                <button type="button" class="load-more-btn px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 border border-blue-200 hover:border-blue-300 rounded-lg transition-colors">
                    Load More
                </button>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                @switch($type)
                    @case('portfolio')
                        <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="w-8 h-8 opacity-50" onerror="this.parentElement.innerHTML='P'">
                        @break
                    @case('course')
                        <img src="{{ asset('assets/icons/book.svg') }}" alt="" class="w-8 h-8 opacity-50" onerror="this.parentElement.innerHTML='C'">
                        @break
                    @case('discussion')
                        <img src="{{ asset('assets/icons/chat.svg') }}" alt="" class="w-8 h-8 opacity-50" onerror="this.parentElement.innerHTML='D'">
                        @break
                    @case('challenge')
                        <img src="{{ asset('assets/icons/trophy.svg') }}" alt="" class="w-8 h-8 opacity-50" onerror="this.parentElement.innerHTML='Ch'">
                        @break
                @endswitch
            </div>
            
            <h3 class="text-lg font-medium text-slate-900 mb-2">
                {{ $state['title'] }}
            </h3>
            
            <p class="text-slate-600 mb-4 max-w-sm mx-auto">
                {{ $isOwnProfile ? $state['desc_own'] : $state['desc_other'] }}
            </p>

            @if($isOwnProfile)
                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    {{ $state['action'] }}
                </button>
            @endif
        </div>
    @endif
</div>
