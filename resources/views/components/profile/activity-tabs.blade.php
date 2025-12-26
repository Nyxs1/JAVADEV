@props([
    'user',
    'isOwnProfile' => false,
    'portfolioActivities' => [],
    'courseActivities' => [],
    'discussionActivities' => [],
    'challengeActivities' => [],
    'eventHistory' => [],
    'mentorHistory' => [],
])

@php
    $tabVisibility = [
        'events' => $user->getActivityPrivacy('events'),
        'portfolio' => $user->getActivityPrivacy('portfolio'),
        'course' => $user->getActivityPrivacy('course'),
        'discussion' => $user->getActivityPrivacy('discussion'),
        'challenge' => $user->getActivityPrivacy('challenge'),
    ];
    
    $tabLabels = [
        'events' => 'Events',
        'portfolio' => 'Portfolio',
        'course' => 'Courses',
        'discussion' => 'Discussions',
        'challenge' => 'Challenges',
    ];

    // Include mentor tab only if user is mentor
    $showMentorTab = $user->isMentor();
    if ($showMentorTab) {
        $tabVisibility['mentoring'] = $user->getActivityPrivacy('mentoring');
        $tabLabels['mentoring'] = 'Mentoring';
    }

    $tabs = $showMentorTab 
        ? ['events', 'mentoring', 'portfolio', 'course', 'discussion', 'challenge']
        : ['events', 'portfolio', 'course', 'discussion', 'challenge'];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="activity-tabs-container">
    {{-- Tab Navigation --}}
    <div class="border-b border-slate-200">
        <nav class="flex overflow-x-auto" aria-label="Activity Tabs">
            @foreach($tabs as $index => $tab)
                <button type="button"
                    class="activity-tab flex-1 min-w-max px-4 py-3 text-sm font-medium text-center border-b-2 transition-colors focus:outline-none {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-600 hover:text-blue-600 hover:border-blue-300' }}"
                    data-tab="{{ $tab }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    <span class="inline-flex items-center justify-center gap-1.5">
                        <span>{{ $tabLabels[$tab] }}</span>
                        @if($isOwnProfile)
                            <img 
                                src="{{ asset('assets/icons/' . ($tabVisibility[$tab] ? 'globe-blue' : 'lock-gray') . '.svg') }}" 
                                alt=""
                                class="visibility-icon w-4 h-4 cursor-pointer hover:scale-110 transition-transform"
                                data-tab="{{ $tab }}"
                                data-public="{{ $tabVisibility[$tab] ? 'true' : 'false' }}"
                                title="{{ $tabVisibility[$tab] ? 'Visible to everyone' : 'Only visible to you' }}">
                        @endif
                    </span>
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="p-6">
        {{-- Events Tab --}}
        <div id="tab-events" class="activity-content">
            @if($isOwnProfile || $tabVisibility['events'])
                <x-profile.event-history :events="$eventHistory" type="member" :user="$user" :is-own-profile="$isOwnProfile" />
            @else
                <x-profile.tab-locked-state tab="Events" />
            @endif
        </div>

        {{-- Mentoring Tab (only for mentors) --}}
        @if($showMentorTab)
        <div id="tab-mentoring" class="activity-content hidden">
            @if($isOwnProfile || $tabVisibility['mentoring'])
                <x-profile.event-history :events="$mentorHistory" type="mentor" :user="$user" :is-own-profile="$isOwnProfile" />
            @else
                <x-profile.tab-locked-state tab="Mentoring" />
            @endif
        </div>
        @endif

        {{-- Other Tabs --}}
        @foreach(['portfolio', 'course', 'discussion', 'challenge'] as $tab)
            <div id="tab-{{ $tab }}" class="activity-content hidden">
                @if($isOwnProfile || $tabVisibility[$tab])
                    <x-profile.activity-list :activities="${$tab . 'Activities'} ?? []" type="{{ $tab }}" :user="$user" :is-own-profile="$isOwnProfile" />
                @else
                    <x-profile.tab-locked-state tab="{{ $tabLabels[$tab] }}" />
                @endif
            </div>
        @endforeach
    </div>
</div>
