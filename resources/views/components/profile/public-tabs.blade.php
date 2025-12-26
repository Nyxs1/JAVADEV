@props([
    'user',
    'isOwnProfile' => false,
    'portfolioActivities' => [],
    'courseActivities' => [],
    'discussionSummary' => [],
])

@php
    $tabVisibility = [
        'portfolio' => $user->getActivityPrivacy('portfolio'),
        'course' => $user->getActivityPrivacy('course'),
        'discussion' => $user->getActivityPrivacy('discussion'),
    ];
    
    $tabLabels = [
        'portfolio' => 'Portfolio',
        'course' => 'Courses',
        'discussion' => 'Discussions',
    ];

    $tabs = ['portfolio', 'course', 'discussion'];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="public-tabs-container">
    {{-- Tab Navigation --}}
    <div class="border-b border-slate-200">
        <nav class="flex overflow-x-auto" aria-label="Profile Tabs">
            @foreach($tabs as $index => $tab)
                <button type="button"
                    class="public-tab flex-1 min-w-max px-4 py-3 text-sm font-medium text-center border-b-2 transition-colors focus:outline-none {{ $index === 0 ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-600 hover:text-blue-600 hover:border-blue-300' }}"
                    data-tab="{{ $tab }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ $tabLabels[$tab] }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="p-6">
        {{-- Portfolio Tab --}}
        <div id="public-tab-portfolio" class="public-content">
            @if($isOwnProfile || $tabVisibility['portfolio'])
                <x-profile.portfolio-list :activities="$portfolioActivities" :user="$user" :is-own-profile="$isOwnProfile" />
            @else
                <x-profile.tab-locked-state tab="Portfolio" />
            @endif
        </div>

        {{-- Courses Tab (includes challenges as part of learning journey) --}}
        <div id="public-tab-course" class="public-content hidden">
            @if($isOwnProfile || $tabVisibility['course'])
                <x-profile.courses-list :activities="$courseActivities" :user="$user" :is-own-profile="$isOwnProfile" />
            @else
                <x-profile.tab-locked-state tab="Courses" />
            @endif
        </div>

        {{-- Discussions Tab (Summary view) --}}
        <div id="public-tab-discussion" class="public-content hidden">
            @if($isOwnProfile || $tabVisibility['discussion'])
                <x-profile.discussion-summary :summary="$discussionSummary" :user="$user" :is-own-profile="$isOwnProfile" />
            @else
                <x-profile.tab-locked-state tab="Discussions" />
            @endif
        </div>
    </div>
</div>
