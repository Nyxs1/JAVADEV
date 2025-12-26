@extends('layouts.error')

@section('title', '403 - Access Denied')

@section('content')
    <div class="error-card">
        <div class="error-icon error-icon--red">
            <img src="{{ asset('assets/icons/lock.svg') }}" alt="">
        </div>
        
        <div class="error-code">403</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-message">
            {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
        </p>

        <div class="error-actions">
            <a href="javascript:history.back()" class="error-btn error-btn--primary">
                <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="">
                <span>Go Back</span>
            </a>
            <a href="{{ auth()->check() ? route('index') : route('login') }}" class="error-btn error-btn--secondary">
                <span>{{ auth()->check() ? 'Go to Home' : 'Go to Login' }}</span>
            </a>
        </div>
    </div>
@endsection
