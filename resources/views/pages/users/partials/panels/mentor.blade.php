{{-- Mentor Panel Tab --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h1 class="text-2xl font-bold text-slate-900">Mentor Panel</h1>
        <p class="text-slate-600 mt-1">Manage your mentoring events</p>
    </div>

    @if(isset($mentorEvent))
        {{-- Event Detail View --}}
        @include('pages.users.partials.events.mentor.detail')
    @else
        {{-- Events List View --}}
        @include('pages.users.partials.events.mentor.list')
    @endif
</div>