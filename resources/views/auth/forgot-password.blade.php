@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-card auth-card--single">
        <div class="auth-header">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/logos/LOGO-Photoroom.png') }}" alt="JavaDev Logo" class="auth-logo-img">
            </div>
            <h1 class="auth-title">FORGOT PASSWORD</h1>
            <p class="auth-subtitle">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="auth-form" id="forgotPasswordForm" novalidate>
            @csrf

            {{-- Success Message --}}
            @if (session('status'))
                <div class="auth-success-banner">
                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="Success" class="w-5 h-5">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="auth-field">
                <label class="auth-label">Email Address</label>
                <div class="auth-input @error('email') error @enderror" id="emailInput">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/mail.svg') }}" alt="Email" class="w-5 h-5">
                    </div>
                    <input type="email" name="email" id="email-input" value="{{ old('email') }}" 
                        placeholder="Enter your email address" 
                        autocomplete="email" />
                </div>
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-btn auth-btn--primary" type="submit" id="submitBtn">Send Reset Link</button>

            <div class="auth-bottom auth-bottom--centered">
                <a class="auth-link auth-link--back" href="{{ route('login') }}">
                    <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-4 h-4">
                    <span>Back to Login</span>
                </a>
            </div>
        </form>
    </div>
@endsection
