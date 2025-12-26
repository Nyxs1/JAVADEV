@extends('layouts.app')

@section('title', 'Events')

@section('content')
    <div class="events-page">
        <div class="events-header">
            <h1 class="events-header__title">Events</h1>
            <p class="events-header__subtitle">Join workshops, seminars, and mentoring sessions to level up your skills.</p>
        </div>

        {{-- Filters --}}
        <div class="events-filters" data-event-filters>
            <form action="{{ route('events.index') }}" method="GET" class="events-filters__form">
                <div class="events-filters__search">
                    <img src="{{ asset('assets/icons/search.svg') }}" alt="" class="events-filters__search-icon">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..."
                        class="events-filters__input">
                </div>
                <div class="events-filters__selects">
                    <select name="status" class="events-filters__select">
                        <option value="">All Status</option>
                        <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Ended</option>
                    </select>
                    <select name="mode" class="events-filters__select">
                        <option value="">All Modes</option>
                        <option value="online" {{ request('mode') === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="onsite" {{ request('mode') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="hybrid" {{ request('mode') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    <button type="submit" class="btn btn--primary">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'mode']))
                        <a href="{{ route('events.index') }}" class="btn btn--outline">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Events Grid --}}
        @if($events->isEmpty())
            <div class="events-empty">
                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="events-empty__icon">
                <h2 class="events-empty__title">No events found</h2>
                <p class="events-empty__text">Check back later for upcoming events or try adjusting your filters.</p>
            </div>
        @else
            <div class="events-grid">
                @foreach($events as $event)
                    <a href="{{ route('events.show', $event) }}" class="event-card">
                        {{-- Cover Image (always rendered, with fallback) --}}
                        <div class="event-card__cover">
                            @if($event->cover_image)
                                <img src="{{ asset('storage/' . $event->cover_image) }}" alt="" class="event-card__cover-img"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="event-card__cover-fallback" style="display: none;">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-card__cover-icon">
                                </div>
                            @else
                                <div class="event-card__cover-fallback">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-card__cover-icon">
                                </div>
                            @endif
                        </div>

                        <div class="event-card__body">
                            <div class="event-card__header">
                                <div class="event-card__badges">
                                    <span
                                        class="event-card__badge event-card__badge--{{ $event->type }}">{{ ucfirst($event->type) }}</span>
                                    <span
                                        class="event-card__badge event-card__badge--{{ $event->mode }}">{{ ucfirst($event->mode) }}</span>
                                </div>
                                <span
                                    class="event-card__status event-card__status--{{ $event->isEnded() ? 'ended' : ($event->isOngoing() ? 'ongoing' : 'upcoming') }}">
                                    {{ $event->status_label }}
                                </span>
                            </div>

                            <h3 class="event-card__title">{{ $event->title }}</h3>
                            <p class="event-card__description">{{ Str::limit($event->description, 100) }}</p>

                            <div class="event-card__meta">
                                <div class="event-card__meta-left">
                                    <div class="event-card__meta-item">
                                        <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="event-card__meta-icon">
                                        <span>{{ $event->start_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="event-card__meta-item">
                                        <img src="{{ asset('assets/icons/clock.svg') }}" alt="" class="event-card__meta-icon">
                                        <span>{{ $event->start_at->format('H:i') }}</span>
                                    </div>
                                </div>
                                @include('events.partials.level-signal', ['level' => $event->level])
                            </div>

                            <div class="event-card__footer">
                                <div class="event-card__social">
                                    @include('events.partials.avatar-stack', [
                                        'participants' => $event->participants,
                                        'totalCount' => $event->participants_count
                                    ])
                                    @if($event->participants_count > 0)
                                        <span class="event-card__joined">{{ $event->participants_count }} joined</span>
                                    @endif
                                </div>
                            @if($event->capacity)
                                <span class="event-card__capacity {{ $event->isFull() ? 'event-card__capacity--full' : '' }}">
                                @if($event->isFull())
                                    Full
                                @else
                                        {{ $event->getRemainingSpots() }} left
                                    @endif
                                </span>
                            @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="events-pagination">
                {{ $events->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection