{{-- Admin Panel Tab --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Admin Panel</h1>
                <p class="text-slate-600 mt-1">Manage events and system settings</p>
            </div>
            <div class="flex items-center gap-3">
                
                @if(($adminSection ?? 'events') === 'events' && !isset($adminEvent))
                    <button type="button" id="create-event-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Create Event
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Section Tabs --}}
    @if(!isset($adminEvent))
        <div class="flex gap-2">
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'section' => 'events']) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSection ?? 'events') === 'events' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Events
            </a>
            <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin', 'section' => 'finalization']) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ ($adminSection ?? '') === 'finalization' ? 'bg-blue-100 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">
                Finalization
            </a>
        </div>
    @endif

    {{-- Content --}}
    @if(isset($adminEvent))
        @include('pages.users.partials.events.shared.detail')
    @elseif(($adminSection ?? 'events') === 'events')
        @include('pages.users.partials.events.shared.list')
    @elseif(($adminSection ?? '') === 'finalization')
        @include('pages.users.partials.finalization.index')
    @endif
</div>

{{-- Create Event Modal --}}
@include('pages.users.partials.events.shared.modal')