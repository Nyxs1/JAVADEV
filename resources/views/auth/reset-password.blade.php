@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="auth-card auth-card--single">
        <div class="auth-header">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/logos/LOGO-Photoroom.png') }}" alt="JavaDev Logo" class="auth-logo-img">
            </div>
            <h1 class="auth-title">RESET PASSWORD</h1>
            <p class="auth-subtitle">Create a new password for your account.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="auth-form" id="resetPasswordForm" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="auth-field">
                <label class="auth-label">Email Address</label>
                <div class="auth-input @error('email') error @enderror" id="emailInput">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/mail.svg') }}" alt="Email" class="w-5 h-5">
                    </div>
                    <input type="email" name="email" id="email-input" 
                        value="{{ $email ?? old('email') }}" 
                        placeholder="Enter your email address"
                        autocomplete="email" />
                </div>
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label">New Password</label>
                <div class="auth-input @error('password') error @enderror" id="passwordWrapper">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/lock.svg') }}" alt="Lock" class="w-5 h-5">
                    </div>
                    <input type="password" name="password" id="password-input" 
                        placeholder="Enter new password" 
                        autocomplete="new-password" />
                    <button type="button" class="auth-input-toggle" id="password-toggle">
                        <img src="{{ asset('assets/icons/eye-icon.svg') }}" alt="Show Password" id="eye-icon" class="w-5 h-5">
                    </button>
                </div>
                {{-- Password Requirements --}}
                <div class="password-requirements" id="passwordRequirements">
                    <div class="requirement" data-req="length">
                        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="req-icon req-icon--valid">
                        <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="req-icon req-icon--invalid">
                        <span>At least 8 characters</span>
                    </div>
                    <div class="requirement" data-req="uppercase">
                        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="req-icon req-icon--valid">
                        <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="req-icon req-icon--invalid">
                        <span>At least 1 uppercase letter</span>
                    </div>
                    <div class="requirement" data-req="number">
                        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="req-icon req-icon--valid">
                        <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="req-icon req-icon--invalid">
                        <span>At least 1 number</span>
                    </div>
                    <div class="requirement" data-req="special">
                        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="req-icon req-icon--valid">
                        <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="req-icon req-icon--invalid">
                        <span>At least 1 special character</span>
                    </div>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">Confirm Password</label>
                <div class="auth-input" id="confirmWrapper">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/lock.svg') }}" alt="Lock" class="w-5 h-5">
                    </div>
                    <input type="password" name="password_confirmation" id="confirm-input" 
                        placeholder="Confirm new password" 
                        autocomplete="new-password" />
                    <button type="button" class="auth-input-toggle" id="confirm-toggle">
                        <img src="{{ asset('assets/icons/eye-icon.svg') }}" alt="Show Password" id="confirm-eye-icon" class="w-5 h-5">
                    </button>
                </div>
                <div class="password-match-indicator" id="matchIndicator">
                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="match-icon match-icon--valid">
                    <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="match-icon match-icon--invalid">
                    <span class="match-text"></span>
                </div>
            </div>

            <button class="auth-btn auth-btn--primary" type="submit" id="submitBtn" disabled>Reset Password</button>

            <div class="auth-bottom auth-bottom--centered">
                <a class="auth-link auth-link--back" href="{{ route('login') }}">
                    <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-4 h-4">
                    <span>Back to Login</span>
                </a>
            </div>
        </form>
    </div>
@endsection
