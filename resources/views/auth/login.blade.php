@extends('layouts.auth')

@section('content')
    <div class="auth-card auth-card--single">
        <div class="auth-header">
            <div class="auth-logo">
                <img src="{{ asset('assets/images/logos/LOGO-Photoroom.png') }}" alt="JavaDev Logo" class="auth-logo-img">
            </div>
            <h1 class="auth-title">WELCOME TO JAVADEV</h1>
            <p class="auth-subtitle">Masuk dan mulai perjalanan belajarmu!</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="auth-form" id="loginForm" novalidate>
            @csrf

            {{-- Success Status Message (e.g., after password reset) --}}
            @if (session('status'))
                <div class="auth-success-banner">
                    <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="Success" class="w-5 h-5">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Auth Error Message --}}
            @if ($errors->has('auth') || $errors->has('login'))
                <div class="auth-error-banner" id="authErrorBanner">
                    <img src="{{ asset('assets/icons/alert-circle.svg') }}" alt="Error" class="w-5 h-5">
                    <span>{{ $errors->first('auth') ?: $errors->first('login') }}</span>
                </div>
            @endif

            <div class="auth-field">
                <label class="auth-label">Email or Username</label>
                <div class="auth-input @error('auth') error @enderror" id="loginInput">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/user-icon.svg') }}" alt="User" class="w-5 h-5">
                    </div>
                    <input type="text" name="login" id="login-input" value="{{ old('login') }}"
                        placeholder="Enter your email or username" autocomplete="username" />
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-label">Password</label>
                <div class="auth-input @error('auth') error @enderror" id="passwordWrapper">
                    <div class="auth-input-icon">
                        <img src="{{ asset('assets/icons/lock-icon.svg') }}" alt="Lock" class="w-5 h-5">
                    </div>
                    <input type="password" name="password" id="password-input" placeholder="********"
                        autocomplete="current-password" />
                    <button type="button" class="auth-input-toggle" id="password-toggle">
                        <img src="{{ asset('assets/icons/eye-icon.svg') }}" alt="Show Password" id="eye-icon"
                            class="w-5 h-5">
                    </button>
                </div>
            </div>

            <div class="auth-row">
                <label class="auth-check">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a class="auth-link" href="{{ route('password.request') }}">Forgot Password?</a>
            </div>

            <button class="auth-btn auth-btn--primary" type="submit" id="submitBtn">SIGN IN</button>

            <div class="auth-divider">
                <span>Or Continue with</span>
            </div>

            <div class="auth-social">
                <button type="button" class="auth-social-btn" id="google-login" title="Continue with Google">
                    <img src="{{ asset('assets/icons/google-icon.svg') }}" alt="Google" class="w-5 h-5">
                </button>
                <button type="button" class="auth-social-btn" id="github-login" title="Continue with GitHub">
                    <img src="{{ asset('assets/icons/github-icon.svg') }}" alt="GitHub" class="w-5 h-5">
                </button>
            </div>

            <div class="auth-bottom">
                <span>No Account yet?</span>
                <a class="auth-link" href="{{ route('register') }}">SIGN UP</a>
            </div>
        </form>
    </div>
@endsection