{{-- Admin Event Detail --}}
<div class="space-y-6">
    {{-- Back Link --}}
    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin']) }}"
        class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-slate-900">
        <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-4 h-4">
        <span>Back to Events</span>
    </a>

    {{-- Event Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-start gap-4">
            @if($adminEvent->cover_image)
                <img src="{{ asset('storage/' . $adminEvent->cover_image) }}" alt=""
                    class="w-24 h-18 rounded-lg object-cover">
            @endif
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900">{{ $adminEvent->title }}</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $adminEvent->start_at->format('d M Y, H:i') }} -
                    {{ $adminEvent->end_at->format('H:i') }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        {{ $adminEvent->status === 'draft' ? 'bg-slate-100 text-slate-600' : '' }}
                        {{ $adminEvent->isEnded() ? 'bg-slate-100 text-slate-600' : '' }}
                        {{ $adminEvent->isOngoing() ? 'bg-green-100 text-green-700' : '' }}
                        {{ $adminEvent->isUpcoming() ? 'bg-blue-100 text-blue-700' : '' }}">
                        {{ $adminEvent->status_label }}
                    </span>
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                        {{ ucfirst($adminEvent->type) }}
                    </span>
                </div>
            </div>
            <a href="{{ route('events.show', $adminEvent->slug) }}" target="_blank"
                class="px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                View Public Page
            </a>
        </div>
    </div>

    {{-- Subtabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="border-b border-slate-200">
            <nav class="flex gap-1 p-2">
                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $adminEvent->slug, 'subtab' => 'edit']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSubtab ?? 'edit') === 'edit' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    Edit
                </a>
                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $adminEvent->slug, 'subtab' => 'mentors']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSubtab ?? '') === 'mentors' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    Mentors
                </a>
                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $adminEvent->slug, 'subtab' => 'requirements']) }}"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSubtab ?? '') === 'requirements' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    Requirements
                </a>
                @if($adminEvent->isEnded())
                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'event' => $adminEvent->slug, 'subtab' => 'reviews']) }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSubtab ?? '') === 'reviews' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Reviews
                    </a>
                @endif
            </nav>
        </div>

        <div class="p-6">
            @if(($adminSubtab ?? 'edit') === 'edit')
                @include('pages.users.partials.events.shared.form')
            @elseif(($adminSubtab ?? '') === 'mentors')
                @include('pages.users.partials.events.shared.mentors')
            @elseif(($adminSubtab ?? '') === 'requirements')
                @include('pages.users.partials.events.shared.requirements')
            @elseif(($adminSubtab ?? '') === 'reviews')
                @include('pages.users.partials.events.shared.reviews')
            @endif
        </div>
    </div>
</div>