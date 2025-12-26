@extends('layouts.app')

@section('title', ucfirst($event->type))

@section('content')
<div class="event-detail" data-event-tabs>
    {{-- Back Link --}}
    <a href="{{ route('events.index') }}" class="event-back">
        <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="event-back__icon">
        Back to Events
    </a>

    {{-- Event Header --}}
    <div class="event-header">
        {{-- Cover Image (always rendered, with fallback) --}}
        <div class="event-header__cover">
            @if($event->cover_image)
                <img src="{{ asset('storage/' . $event->cover_image) }}" 
                     alt="" 
                     class="event-header__cover-img"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="event-header__cover-fallback" style="display: none;">
                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-header__cover-icon">
                </div>
            @else
                <div class="event-header__cover-fallback">
                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-header__cover-icon">
                </div>
            @endif
        </div>
        <div class="event-header__content">
            {{-- Row 1: Badges LEFT | Status RIGHT --}}
            <div class="event-header__top">
                <div class="event-badges">
                    <span class="event-badge event-badge--{{ $event->type }}">{{ ucfirst($event->type) }}</span>
                    <span class="event-badge event-badge--{{ $event->mode }}">{{ ucfirst($event->mode) }}</span>
                </div>
                <div class="event-header__status">
                    @if($event->isEnded())
                        <span class="event-status-badge event-status-badge--ended">Ended</span>
                    @elseif($event->isOngoing())
                        <span class="event-status-badge event-status-badge--ongoing">Ongoing</span>
                    @else
                        <span class="event-status-badge event-status-badge--upcoming">Upcoming</span>
                    @endif
                </div>
            </div>

            <h1 class="event-title">{{ $event->title }}</h1>
            <p class="event-description">{{ Str::limit($event->description, 200) }}</p>
            
            {{-- Meta Bar: Centered info + Signal pinned right --}}
            <div class="event-meta-bar">
                <div class="event-meta__center">
                    <div class="event-meta__item">
                        <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-meta__icon">
                        <span>{{ $event->start_at->format('D, d M Y') }}</span>
                    </div>
                    <div class="event-meta__item">
                        <img src="{{ asset('assets/icons/clock.svg') }}" alt="" class="event-meta__icon">
                        <span>{{ $event->start_at->format('H:i') }} - {{ $event->end_at->format('H:i') }}</span>
                    </div>
                    @if($event->location_text)
                    <div class="event-meta__item">
                        <img src="{{ asset('assets/icons/location-marker.png') }}" alt="" class="event-meta__icon">
                        <span>{{ $event->location_text }}</span>
                    </div>
                    @endif
                    <div class="event-meta__item">
                        <img src="{{ asset('assets/icons/user.svg') }}" alt="" class="event-meta__icon">
                        <span>{{ $participantCount }}{{ $event->capacity ? '/' . $event->capacity : '' }} participants</span>
                    </div>
                </div>
                <div class="event-meta__signal">
                    @include('events.partials.level-signal', ['level' => $event->level])
                </div>
            </div>

            {{-- CTA Row: Single button based on state --}}
            <div class="event-cta">
                <div class="event-cta__actions">
                @guest
                    <a href="{{ route('login') }}" class="btn btn--primary btn--lg">
                        Login to Join
                    </a>
                @else
                    @if($event->isEnded())
                        {{-- ENDED: Show "Leave a Review" if joined and attended, else "Event Ended" --}}
                        @if($isRegistered && $isCheckedIn && !$hasReviewed)
                            <button class="btn btn--primary btn--lg" data-tab-trigger="reviews">
                                Leave a Review
                            </button>
                        @else
                            <span class="event-cta__status event-cta__status--ended">
                                <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-cta__icon">
                                Event Ended
                            </span>
                        @endif
                    @elseif($event->isOngoing())
                        {{-- ONGOING: Show check-in or status --}}
                        @if($isRegistered)
                            @if($canCheckIn)
                                <form action="{{ route('events.check-in', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn--primary btn--lg">
                                        Check In
                                    </button>
                                </form>
                            @elseif($isCheckedIn)
                                <span class="event-cta__badge">
                                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-cta__icon">
                                    Checked In
                                </span>
                            @else
                                <span class="event-cta__badge">
                                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-cta__icon">
                                    Event Ongoing
                                </span>
                            @endif
                        @else
                            <span class="event-cta__status event-cta__status--ongoing">
                                <img src="{{ asset('assets/icons/clock.svg') }}" alt="" class="event-cta__icon">
                                Event Ongoing
                            </span>
                        @endif
                    @elseif($isRegistered)
                        {{-- UPCOMING + JOINED: Show "Registered" with cancel option --}}
                        <div class="event-cta__registered">
                            <span class="event-cta__badge">
                                <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-cta__icon">
                                Registered
                            </span>
                            @if($canCancel)
                            <form action="{{ route('events.cancel', $event) }}" method="POST" class="event-cta__cancel-form">
                                @csrf
                                <button type="submit" class="btn btn--outline btn--sm">Cancel</button>
                            </form>
                            @endif
                        </div>
                    @elseif($isFull)
                        {{-- UPCOMING + FULL --}}
                        <span class="event-cta__status event-cta__status--full">
                            <img src="{{ asset('assets/icons/alert-circle.svg') }}" alt="" class="event-cta__icon">
                            Event Full
                        </span>
                    @elseif($canJoin)
                        {{-- UPCOMING + NOT JOINED: Show "Join Event" --}}
                        <form action="{{ route('events.join', $event) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn--primary btn--lg">
                                Join Event
                                @if($remainingSpots !== null && $remainingSpots <= 10)
                                <span class="btn__badge">{{ $remainingSpots }} spots left</span>
                                @endif
                            </button>
                        </form>
                    @endif
                @endguest
                </div>
                @if($participantCount > 0)
                <div class="event-cta__avatars">
                    @include('events.partials.avatar-stack', [
                        'participants' => $participantsPreview,
                        'totalCount' => $participantCount
                    ])
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="event-tabs">
        <nav class="event-tabs__nav" role="tablist">
            <button class="event-tabs__btn active" data-tab="about" role="tab" aria-selected="true">
                About
            </button>
            <button class="event-tabs__btn" data-tab="requirements" role="tab" aria-selected="false">
                Requirements
            </button>
            <button class="event-tabs__btn" data-tab="mentor" role="tab" aria-selected="false">
                Mentor
            </button>
            <button class="event-tabs__btn" data-tab="reviews" role="tab" aria-selected="false">
                Reviews
                @if($event->isEnded() && $reviewCount > 0)
                <span class="event-tabs__count">{{ $reviewCount }}</span>
                @elseif(!$event->isEnded())
                <img src="{{ asset('assets/icons/lock.svg') }}" alt="Locked" class="event-tabs__lock">
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab Panels --}}
    <div class="event-panels">
        {{-- About Tab --}}
        <div class="event-panel active" id="tab-about" role="tabpanel">
            <div class="event-about">
                <h2 class="event-section__title">About This Event</h2>
                <div class="event-about__content">
                    {!! nl2br(e($event->description)) !!}
                </div>

                @if($event->mode !== 'onsite' && $event->meeting_url)
                <div class="event-about__meeting">
                    <h3>Meeting Link</h3>
                    <p>The meeting link will be shared with registered participants before the event starts.</p>
                </div>
                @endif

                {{-- Reflection Section (only for attended participants after event ends) --}}
                @auth
                    @if($event->isEnded() && $isCheckedIn)
                    <div class="event-about__reflection">
                        <h3>Your Reflection</h3>
                        @if($hasReflection)
                            <div class="event-about__reflection-content">
                                <p>{{ $participant->reflection }}</p>
                            </div>
                        @elseif($canSubmitReflection)
                            <form action="{{ route('events.reflection', $event) }}" method="POST" class="event-about__reflection-form">
                                @csrf
                                <textarea name="reflection" rows="4" placeholder="Share your thoughts about this event..." required>{{ old('reflection') }}</textarea>
                                @error('reflection')
                                <span class="event-about__reflection-error">{{ $message }}</span>
                                @enderror
                                <button type="submit" class="btn btn--secondary">Submit Reflection</button>
                            </form>
                        @endif
                    </div>
                    @endif

                    {{-- Certificate Section --}}
                    @if($participant && $participant->canReceiveCertificate())
                    <div class="event-about__certificate">
                        <h3>Certificate</h3>
                        <a href="{{ route('events.certificate', $event) }}" class="btn btn--primary">
                            <img src="{{ asset('assets/icons/download.svg') }}" alt="" class="btn__icon">
                            Download Certificate
                        </a>
                    </div>
                    @endif
                @endauth
            </div>
        </div>

        {{-- Requirements Tab --}}
        <div class="event-panel" id="tab-requirements" role="tabpanel">
            @include('events.partials.requirements', [
                'infoRequirements' => $infoRequirements,
                'checklistRequirements' => $checklistRequirements,
                'techRequirements' => $techRequirements,
                'userChecks' => $userChecks,
                'isRegistered' => $isRegistered,
                'event' => $event,
            ])
            
            @can('updateRequirements', $event)
            <div class="event-requirements__actions">
                <a href="{{ route('events.requirements.edit', $event) }}" class="btn btn--secondary">
                    <img src="{{ asset('assets/icons/settings.svg') }}" alt="" class="btn__icon">
                    Edit Requirements
                </a>
            </div>
            @endcan
        </div>

        {{-- Mentor Tab --}}
        <div class="event-panel" id="tab-mentor" role="tabpanel">
            @include('events.partials.mentors', ['mentors' => $event->mentors])
        </div>

        {{-- Reviews Tab --}}
        <div class="event-panel" id="tab-reviews" role="tabpanel">
            @if($event->isEnded())
                @include('events.partials.reviews', [
                    'feedback' => $event->feedback,
                    'avgRating' => $avgRating,
                    'reviewCount' => $reviewCount,
                    'canReview' => $canReview,
                    'hasReviewed' => $hasReviewed,
                    'event' => $event,
                ])
            @else
                {{-- Locked state --}}
                <div class="event-empty">
                    <img src="{{ asset('assets/icons/lock.svg') }}" alt="" class="event-empty__icon">
                    <p class="event-empty__text">Reviews akan terbuka setelah event selesai.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection