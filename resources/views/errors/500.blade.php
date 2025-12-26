@extends('layouts.error')

@section('title', '500 - Server Error')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--red">
            <img src="{{ asset('assets/icons/alert-circle.svg') }}" alt="">
        </div>
        
        <div class="error-code">500</div>
        <h1 class="error-title">Server Error</h1>
        <p class="error-message">
            Something went wrong on our end. Please try again later or contact support if the problem persists.
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
