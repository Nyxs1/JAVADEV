@extends('layouts.auth')

@section('body-class', 'register-body')
@section('full-layout', true)

@section('content')
    <div class="register-container">
        <div class="register-card">

            {{-- LEFT SIDE --}}
            <div class="register-form-side">
                <div class="register-header">
                    <div class="register-logo">
                        <img src="{{ asset('assets/images/logos/LOGO-Photoroom.png') }}" alt="JavaDev"
                            class="register-logo-img">
                    </div>

                    <h1 class="register-title">Buat akun kamu</h1>
                    <p class="register-subtitle">Mulai bangun portfolio developer kamu sekarang.</p>
                </div>

                <form id="registerForm" class="register-form" novalidate>
                    @csrf

                    <div class="register-field">
                        <label class="register-label">Username</label>
                        <div class="register-input" id="usernameInput">
                            <div class="register-input-icon">
                                <img src="{{ asset('assets/icons/user-icon.svg') }}" alt="User" class="w-5 h-5">
                            </div>
                            <input type="text" name="username" value="{{ old('username') }}" placeholder="johndoe" />
                        </div>
                        <div class="register-error" id="usernameError" style="display: none;"></div>
                    </div>

                    <div class="register-field">
                        <label class="register-label">Email Address</label>
                        <div class="register-input" id="emailInput">
                            <div class="register-input-icon">
                                <img src="{{ asset('assets/icons/email-icon.svg') }}" alt="Email" class="w-5 h-5">
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" />
                            <button type="button" class="btn-get-code" id="btnGetCode">
                                <span class="btn-get-code__text">Get Code</span>
                                <img src="{{ asset('assets/icons/paper-plane.svg') }}" alt="" class="btn-get-code__icon">
                            </button>
                        </div>
                        <div class="register-error" id="emailError" style="display: none;"></div>

                        {{-- Dev OTP Panel (inline, left side) --}}
                        <div id="devOtpPanel" class="dev-otp-panel hidden"></div>
                    </div>

                    {{-- Verification Code Field --}}
                    <div class="register-field" id="verificationBlock">
                        <label class="register-label">Verification Code</label>
                        <div class="register-input" id="verificationInput">
                            <div class="register-input-icon">
                                <img src="{{ asset('assets/icons/key.svg') }}" alt="Key" class="w-5 h-5">
                            </div>
                            <input type="text" id="verificationCode" name="verification_code" placeholder="123456"
                                inputmode="numeric" maxlength="6" autocomplete="one-time-code">
                        </div>
                        <div class="register-error" id="verificationError" style="display: none;"></div>
                    </div>

                    <div class="register-row">
                        <div class="register-field register-field-half">
                            <label class="register-label">Password</label>
                            <div class="register-input" id="passwordInput">
                                <div class="register-input-icon">
                                    <img src="{{ asset('assets/icons/lock-icon.svg') }}" alt="Lock" class="w-5 h-5">
                                </div>
                                <input type="password" name="password" placeholder="********"
                                    id="register-password-input" />
                                <button type="button" class="register-input-toggle" id="password-toggle">
                                    <img src="{{ asset('assets/icons/eye-icon.svg') }}" alt="Show Password"
                                        id="register-eye-icon" class="w-5 h-5">
                                </button>
                            </div>
                            <div class="register-error" id="passwordError" style="display: none;"></div>
                            <div class="password-strength" id="passwordStrength" style="display: none;">
                                <div class="password-strength__track">
                                    <div class="password-strength__bar"></div>
                                </div>
                                <span class="password-strength__text"></span>
                            </div>
                            <div class="password-hint">Min. 8 characters with uppercase, lowercase, and number.</div>
                        </div>

                        <div class="register-field register-field-half">
                            <label class="register-label">Confirm</label>
                            <div class="register-input" id="confirmPasswordInput">
                                <div class="register-input-icon">
                                    <img src="{{ asset('assets/icons/check-circle-outline-icon.svg') }}" alt="Confirm"
                                        class="w-5 h-5">
                                </div>
                                <input type="password" name="password_confirmation" placeholder="********"
                                    id="confirm-password-input" />
                                <button type="button" class="register-input-toggle" id="confirm-toggle">
                                    <img src="{{ asset('assets/icons/eye-icon.svg') }}" alt="Show Password"
                                        id="confirm-eye-icon" class="w-5 h-5">
                                </button>
                            </div>
                            <div class="register-error" id="confirmPasswordError" style="display: none;"></div>
                        </div>
                    </div>

                    <button class="register-btn" type="submit" id="submitBtn">SIGN UP</button>

                    <div class="register-error register-error-global" id="globalError" style="display: none;"></div>

                    <div class="register-bottom">
                        <span>Sudah punya akun? </span>
                        <a class="register-link" href="{{ route('login') }}">SIGN IN</a>
                    </div>
                </form>
            </div>

            {{-- RIGHT SIDE --}}
            <div class="register-illustration-side">
                <div class="register-illustration">

                    {{-- ICON GROUP --}}
                    <div class="register-floating-icons">
                        <div class="register-icon register-icon-terminal">
                            <img src="{{ asset('assets/icons/terminal-large-icon.svg') }}" alt="Terminal">
                        </div>

                        <div class="register-icon register-icon-code">
                            <img src="{{ asset('assets/icons/code-brackets-icon.svg') }}" alt="Code">
                        </div>

                        <div class="register-icon register-icon-trophy">
                            <img src="{{ asset('assets/icons/trophy-icon.svg') }}" alt="Trophy">
                        </div>
                    </div>

                    <h2 class="register-illustration-title">Level Up Your Career</h2>
                    <p class="register-illustration-text">
                        Join a thriving ecosystem where code meets collaboration. Build your portfolio, find mentors, and
                        ship projects.
                    </p>

                    <div class="register-features">
                        <div class="register-feature">
                            <div class="register-feature-icon">
                                <img src="{{ asset('assets/icons/graduation-cap-icon.svg') }}" alt="Mentorship">
                            </div>
                            <div class="register-feature-content">
                                <h3>Mentorship</h3>
                                <p>Learn from experts</p>
                            </div>
                        </div>

                        <div class="register-feature">
                            <div class="register-feature-icon">
                                <img src="{{ asset('assets/icons/rocket-icon.svg') }}" alt="Projects">
                            </div>
                            <div class="register-feature-content">
                                <h3>Projects</h3>
                                <p>Build together</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection