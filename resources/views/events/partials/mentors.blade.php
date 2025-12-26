<div class="event-mentors">
    <h2 class="event-section__title">Mentors & Speakers</h2>

    @if($mentors->isEmpty())
        <div class="event-empty">
            <img src="{{ asset('assets/icons/user.svg') }}" alt="" class="event-empty__icon">
            <p class="event-empty__text">Informasi mentor akan diumumkan segera.</p>
        </div>
    @else
        <div class="event-mentors__grid">
            @foreach($mentors as $mentor)
            <div class="event-mentor-card">
                <div class="event-mentor-card__avatar">
                    @if($mentor->user->avatar)
                        <img src="{{ asset('storage/' . $mentor->user->avatar) }}" alt="{{ $mentor->user->name }}">
                    @else
                        <div class="event-mentor-card__avatar-placeholder">
                            {{ strtoupper(substr($mentor->user->name ?? $mentor->user->username, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="event-mentor-card__info">
                    <h3 class="event-mentor-card__name">{{ $mentor->user->name ?? $mentor->user->username }}</h3>
                    <span class="event-mentor-card__role event-mentor-card__role--{{ $mentor->role }}">
                        {{ $mentor->role_label }}
                    </span>
                    @if($mentor->user->bio)
                    <p class="event-mentor-card__bio">{{ Str::limit($mentor->user->bio, 100) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
