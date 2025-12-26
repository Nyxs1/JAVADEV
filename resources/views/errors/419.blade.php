@extends('layouts.error')

@section('title', '419 - Session Expired')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--yellow">
            <img src="{{ asset('assets/icons/warning.svg') }}" alt="">
        </div>
        
        <div class="error-code">419</div>
        <h1 class="error-title">Session Expired</h1>
        <p class="error-message">
            Your session has expired. Please refresh the page and try again.
        </p>

        <div class="error-actions">
            <a href="javascript:location.reload()" class="error-btn error-btn--primary">
                <img src="{{ asset('assets/icons/refresh.svg') }}" alt="">
                <span>Refresh Page</span>
            </a>
            <a href="{{ route('login') }}" class="error-btn error-btn--secondary">
                <span>Go to Login</span>
            </a>
        </div>
    </div>
@endsection
