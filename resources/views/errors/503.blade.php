@extends('layouts.error')

@section('title', '503 - Service Unavailable')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--yellow">
            <img src="{{ asset('assets/icons/warning.svg') }}" alt="">
        </div>
        
        <div class="error-code">503</div>
        <h1 class="error-title">Service Unavailable</h1>
        <p class="error-message">
            We are currently performing maintenance. Please check back soon.
        </p>

        <div class="error-actions">
            <a href="javascript:location.reload()" class="error-btn error-btn--primary">
                <img src="{{ asset('assets/icons/refresh.svg') }}" alt="">
                <span>Try Again</span>
            </a>
        </div>
    </div>
@endsection
