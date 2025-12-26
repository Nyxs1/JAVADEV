@extends('layouts.app')

@section('title', 'My Events')

@section('content')
<div class="my-events" data-my-events-tabs>
    <div class="my-events__header">
        <h1 class="my-events__title">My Events</h1>
        <p class="my-events__subtitle">Track your event participation and mentoring history.</p>
    </div>

    {{-- Tabs --}}
    <div class="my-events__tabs">
        <button class="my-events__tab active" data-tab="upcoming">
            Upcoming
            @php
                $upcomingCount = $upcomingParticipant->count() + $upcomingMentor->count();
            @endphp
            @if($upcomingCount > 0)
            <span class="my-events__tab-count">{{ $upcomingCount }}</span>
            @endif
        </button>
        <button class="my-events__tab" data-tab="past">
            Past
            @php
                $pastCount = $pastParticipant->count() + $pastMentor->count();
            @endphp
            @if($pastCount > 0)
            <span class="my-events__tab-count">{{ $pastCount }}</span>
            @endif
        </button>
    </div>

    {{-- Upcoming Panel --}}
    <div class="my-events__panel active" id="panel-upcoming">
        @if($upcomingParticipant->isEmpty() && $upcomingMentor->isEmpty())
        <div class="my-events__empty">
            <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="my-events__empty-icon">
            <h3>No upcoming events</h3>
            <p>Browse events and join one to get started!</p>
            <a href="{{ route('events.index') }}" class="btn btn--primary">Browse Events</a>
        </div>
        @else
            {{-- Mentor Events --}}
            @if($upcomingMentor->isNotEmpty())
            <div class="my-events__section">
                <h2 class="my-events__section-title">
                    <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="my-events__section-icon">
                    As Mentor
                </h2>
                <div class="my-events__list">
                    @foreach($upcomingMentor as $mentorEvent)
                    @include('events.partials.my-event-card', [
                        'event' => $mentorEvent->event,
                        'role' => $mentorEvent->role,
                        'isMentor' => true,
                    ])
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Participant Events --}}
            @if($upcomingParticipant->isNotEmpty())
            <div class="my-events__section">
                <h2 class="my-events__section-title">
                    <img src="{{ asset('assets/icons/user.svg') }}" alt="" class="my-events__section-icon">
                    As Participant
                </h2>
                <div class="my-events__list">
                    @foreach($upcomingParticipant as $participation)
                    @include('events.partials.my-event-card', [
                        'event' => $participation->event,
                        'attendance' => $participation->attendance_status,
                        'isMentor' => false,
                    ])
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>

    {{-- Past Panel --}}
    <div class="my-events__panel" id="panel-past">
        @if($pastParticipant->isEmpty() && $pastMentor->isEmpty())
        <div class="my-events__empty">
            <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="my-events__empty-icon">
            <h3>No past events</h3>
            <p>Your completed events will appear here.</p>
        </div>
        @else
            {{-- Mentor Events --}}
            @if($pastMentor->isNotEmpty())
            <div class="my-events__section">
                <h2 class="my-events__section-title">
                    <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="my-events__section-icon">
                    As Mentor
                </h2>
                <div class="my-events__list">
                    @foreach($pastMentor as $mentorEvent)
                    @include('events.partials.my-event-card', [
                        'event' => $mentorEvent->event,
                        'role' => $mentorEvent->role,
                        'isMentor' => true,
                        'isPast' => true,
                    ])
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Participant Events --}}
            @if($pastParticipant->isNotEmpty())
            <div class="my-events__section">
                <h2 class="my-events__section-title">
                    <img src="{{ asset('assets/icons/user.svg') }}" alt="" class="my-events__section-icon">
                    As Participant
                </h2>
                <div class="my-events__list">
                    @foreach($pastParticipant as $participation)
                    @include('events.partials.my-event-card', [
                        'event' => $participation->event,
                        'attendance' => $participation->attendance_status,
                        'isMentor' => false,
                        'isPast' => true,
                    ])
                    @endforeach
                </div>
            </div>
            @endif
        @endif
    </div>
</div>
@endsection