@props([
    'user',
    'isOwnProfile' => false,
    'portfolioActivities' => collect([]),
    'courseActivities' => collect([]),
    'discussionSummary' => [],
])

@php
    // Tab visibility based on published items (item-level privacy)
    // For own profile: show all tabs so owner can see drafts
    // For visitors: show only tabs with published content
    $hasPortfolio = $portfolioActivities->isNotEmpty();
    $hasCourses = $courseActivities->isNotEmpty();
    
    $tabVisibility = [
        'portfolio' => $isOwnProfile || $hasPortfolio,
        'course' => $isOwnProfile || $hasCourses,
        'discussion' => true, // Always visible
    ];
    
    $tabLabels = [
        'portfolio' => 'Portfolio',
        'course' => 'Courses',
        'discussion' => 'Discussions',
    ];

    // Filter visible tabs only
    $visibleTabs = array_keys(array_filter($tabVisibility));
    
    // Find first visible tab for default active state
    $firstVisibleTab = reset($visibleTabs);
@endphp

@if(count($visibleTabs) > 0)
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" id="public-tabs-container">
    {{-- Tab Navigation --}}
    <div class="border-b border-slate-200">
        <nav class="flex overflow-x-auto" aria-label="Profile Tabs">
            @foreach($visibleTabs as $index => $tab)
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
        @if($tabVisibility['portfolio'])
            <div id="public-tab-portfolio" class="public-content {{ $firstVisibleTab !== 'portfolio' ? 'hidden' : '' }}">
                <x-profile.portfolio-list :activities="$portfolioActivities" :user="$user" :is-own-profile="$isOwnProfile" />
            </div>
        @endif

        {{-- Courses Tab --}}
        @if($tabVisibility['course'])
            <div id="public-tab-course" class="public-content {{ $firstVisibleTab !== 'course' ? 'hidden' : '' }}">
                <x-profile.courses-list :activities="$courseActivities" :user="$user" :is-own-profile="$isOwnProfile" />
            </div>
        @endif

        {{-- Discussions Tab (Always visible) --}}
        @if($tabVisibility['discussion'])
            <div id="public-tab-discussion" class="public-content {{ $firstVisibleTab !== 'discussion' ? 'hidden' : '' }}">
                <x-profile.discussion-summary :summary="$discussionSummary" :user="$user" :is-own-profile="$isOwnProfile" />
            </div>
        @endif
    </div>
</div>
@else
    {{-- Fallback: No public activities --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        <h3 class="text-lg font-medium text-slate-900 mb-2">Profil Privat</h3>
        <p class="text-slate-600 max-w-sm mx-auto">
            User ini belum membagikan aktivitas apapun secara publik.
        </p>
    </div>
@endif
