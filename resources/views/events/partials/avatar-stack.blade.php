{{-- Avatar Stack (max 3 + overflow) --}}
@php
    $participants = $participants ?? collect();
    $totalCount = $totalCount ?? 0;
    $maxShow = 3;
    $overflow = max(0, $totalCount - $maxShow);
@endphp

@if($totalCount > 0)
<div class="avatar-stack">
    @foreach($participants->take($maxShow) as $participant)
        @php
            $user = $participant->user;
            $hasAvatar = $user && $user->avatar;
            $initial = $user ? strtoupper(substr($user->username ?? 'U', 0, 1)) : 'U';
        @endphp
        <div class="avatar-stack__item" title="{{ $user->username ?? 'User' }}">
            @if($hasAvatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="avatar-stack__img">
            @else
                <span class="avatar-stack__initial">{{ $initial }}</span>
            @endif
        </div>
    @endforeach
    @if($overflow > 0)
        <div class="avatar-stack__overflow">
            <span>+{{ $overflow }}</span>
        </div>
    @endif
</div>
@endif
