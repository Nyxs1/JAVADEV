@extends('layouts.error')

@section('title', '429 - Too Many Requests')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--yellow">
            <img src="{{ asset('assets/icons/warning.svg') }}" alt="">
        </div>
        
        <div class="error-code">429</div>
        <h1 class="error-title">Too Many Requests</h1>
        <p class="error-message">
            You have made too many requests in a short period. Please wait a moment and try again.
        </p>

        <div class="error-actions">
            <a href="javascript:location.reload()" class="error-btn error-btn--primary">
                <img src="{{ asset('assets/icons/refresh.svg') }}" alt="">
                <span>Try Again</span>
            </a>
            <a href="{{ auth()->check() ? route('index') : route('login') }}" class="error-btn error-btn--secondary">
                <span>{{ auth()->check() ? 'Go to Home' : 'Go to Login' }}</span>
            </a>
        </div>
    </div>
@endsection
