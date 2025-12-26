@extends('layouts.error')

@section('title', '404 - Page Not Found')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--blue">
            <img src="{{ asset('assets/icons/alert-circle.svg') }}" alt="">
        </div>
        
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>

        <div class="error-actions">
            <a href="{{ auth()->check() ? route('index') : route('login') }}" class="error-btn error-btn--primary">
                <span>{{ auth()->check() ? 'Go to Home' : 'Go to Login' }}</span>
            </a>
            <a href="javascript:history.back()" class="error-btn error-btn--secondary">
                <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="">
                <span>Go Back</span>
            </a>
        </div>
    </div>
@endsection
