<div class="my-event-card">
    <div class="my-event-card__header">
        <div class="my-event-card__badges">
            <span class="my-event-card__badge my-event-card__badge--{{ $event->type }}">{{ ucfirst($event->type) }}</span>
            @if($isMentor)
            <span class="my-event-card__badge my-event-card__badge--role">{{ ucfirst($role) }}</span>
            @endif
        </div>
        <span class="my-event-card__status my-event-card__status--{{ $event->isEnded() ? 'ended' : ($event->isOngoing() ? 'ongoing' : 'upcoming') }}">
            {{ $event->status_label }}
        </span>
    </div>

    <h3 class="my-event-card__title">{{ $event->title }}</h3>

    <div class="my-event-card__meta">
        <div class="my-event-card__meta-item">
            <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="my-event-card__meta-icon">
            <span>{{ $event->start_at->format('d M Y, H:i') }}</span>
        </div>
        @if(!$isMentor && isset($attendance))
        <div class="my-event-card__meta-item">
            <img src="{{ asset('assets/icons/' . ($attendance === 'present' ? 'check-circle.svg' : 'x-circle.svg')) }}" alt="" class="my-event-card__meta-icon">
            <span>{{ $attendance === 'present' ? 'Attended' : 'Absent' }}</span>
        </div>
        @endif
    </div>

    <div class="my-event-card__actions">
        <a href="{{ route('events.show', $event) }}" class="btn btn--secondary btn--sm">View Details</a>
        @if(isset($isPast) && $isPast && !$isMentor && $event->isEnded())
            @php
                $hasReviewed = $event->feedback()
                    ->where('from_user_id', auth()->id())
                    ->whereNull('to_user_id')
                    ->exists();
            @endphp
            @if(!$hasReviewed)
            <a href="{{ route('events.show', $event) }}#reviews" class="btn btn--primary btn--sm">Leave Review</a>
            @else
            <span class="my-event-card__reviewed">
                <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="my-event-card__reviewed-icon">
                Reviewed
            </span>
            @endif
        @endif
    </div>
</div>
