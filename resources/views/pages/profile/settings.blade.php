@extends('layouts.app')

@section('title', 'Account Settings')

@php
    $activeTab = request('tab', 'profile');
@endphp

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('users.dashboard', auth()->user()->username) }}"
                    class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4">
                    <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-4 h-4">
                    <span>Back to Dashboard</span>
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Settings</h1>
                <p class="text-slate-600 mt-1">Manage your profile and account settings.</p>
            </div>

            {{-- Flash Messages --}}
            <x-ui.flash-message type="success" :message="session('success')" />
            <x-ui.flash-message type="error" :message="session('error')" />

            {{-- Tab Navigation --}}
            <div class="mb-6">
                <nav class="flex space-x-1 bg-white rounded-lg p-1 shadow-sm border border-slate-200">
                    @foreach(['profile' => 'Profile', 'skills' => 'Skills', 'security' => 'Security', 'account' => 'Account'] as $tab => $label)
                        <a href="?tab={{ $tab }}"
                            class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors
                                    {{ $activeTab === $tab ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- TAB 1: Profile --}}
            @if($activeTab === 'profile')
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Edit Profile</h2>
                        <p class="text-sm text-slate-600 mt-1">Update your profile photo, name, and bio.</p>
                    </div>

                    <div class="p-6">
                        <x-profile.profile-form :user="$user" />
                    </div>
                </div>
            @endif

            {{-- TAB 2: Skills --}}
            @if($activeTab === 'skills')
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Tech Skills</h2>
                        <p class="text-sm text-slate-600 mt-1">Add your technical skills to showcase on your profile.</p>
                    </div>

                    <div class="p-6">
                        {{-- Add New Skill Form --}}
                        <form method="POST" action="{{ route('profile.skills.store') }}" class="mb-6">
                            @csrf
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <input type="text" name="tech_name" placeholder="e.g. PHP, Laravel, MySQL"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required maxlength="100">
                                </div>
                                <div class="w-36">
                                    <x-forms.skill-level-select name="level" :selected="1" />
                                </div>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                                    Add Skill
                                </button>
                            </div>
                            @error('tech_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </form>

                        {{-- Existing Skills List --}}
                        @if($user->skills->count() > 0)
                            <div class="grid gap-3">
                                @foreach($user->skills as $skill)
                                    <x-profile.skill-item :skill="$skill" />
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-500">
                                <p>No skills added yet. Add your first skill above.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- TAB 3: Security --}}
            @if($activeTab === 'security')
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Change Password</h2>
                        <p class="text-sm text-slate-600 mt-1">Make sure your new password is strong and memorable.</p>
                    </div>

                    <form method="POST" action="{{ route('profile.change-password') }}" class="p-6">
                        @csrf

                        {{-- Current Password --}}
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            @error('current_password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div class="mb-4">
                            <label for="new_password" class="block text-sm font-medium text-slate-700 mb-1">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="new_password" id="new_password"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required minlength="8">
                            @error('new_password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 mt-1">Minimum 8 characters.</p>
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="mb-6">
                            <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- TAB 4: Account --}}
            @if($activeTab === 'account')
                {{-- Username Change (Instagram-style) --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Username</h2>
                        <p class="text-sm text-slate-600 mt-1">Your unique identifier on JavaDev.</p>
                    </div>

                    <div class="p-6">
                        <form id="account-form" method="POST" action="{{ route('settings.username.update') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="block text-sm font-medium text-slate-700 mb-1">
                                    Username
                                </label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">@</span>
                                        <input type="text" name="username" id="username"
                                            value="{{ $user->username }}" data-original="{{ $user->username }}"
                                            class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+" required>
                                    </div>
                                    <button type="button" id="check-username-btn"
                                        class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg border border-slate-300 hover:bg-slate-200 transition-colors whitespace-nowrap">
                                        Check
                                    </button>
                                </div>

                                {{-- Status Message --}}
                                <div id="username-status" class="mt-2 hidden">
                                    <div class="flex items-center gap-2">
                                        <img id="username-status-icon" src="" class="w-4 h-4 hidden" alt="">
                                        <span id="username-status-text" class="text-sm"></span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500 mt-2">Letters, numbers, and underscores only. 3-20 characters.</p>
                            </div>

                            {{-- Save Button --}}
                            <div class="flex justify-end">
                                <button type="submit" id="save-username-btn" disabled
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    Save Username
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Account Info (Read-only) --}}
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                    <div class="p-6 border-b border-slate-200">
                        <h2 class="text-lg font-semibold text-slate-900">Account Information</h2>
                        <p class="text-sm text-slate-600 mt-1">Read-only information about your account.</p>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                                {{ $user->email }}
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Email cannot be changed.</p>
                        </div>

                        {{-- Birth Date --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Birth Date</label>
                            <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                                @if($user->birth_date)
                                    {{ \Carbon\Carbon::parse($user->birth_date)->format('d M Y') }}
                                    <span class="text-slate-400">({{ $user->age }} years old)</span>
                                @else
                                    <span class="text-slate-400 italic">Not set</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Birth date is final from onboarding.</p>
                        </div>

                        {{-- Member Since --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Member Since</label>
                            <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600">
                                {{ $user->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Role Management --}}
                <x-profile.role-card :user="$user" />
            @endif

        </div>
    </div>
@endsection