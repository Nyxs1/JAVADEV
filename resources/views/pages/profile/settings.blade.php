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

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="w-5 h-5">
                        <span class="text-green-800">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icons/x-circle.svg') }}" alt="" class="w-5 h-5">
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="mb-6">
                <nav class="flex space-x-1 bg-white rounded-lg p-1 shadow-sm border border-slate-200">
                    <a href="?tab=profile"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors
                                {{ $activeTab === 'profile' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Profile
                    </a>
                    <a href="?tab=skills"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors
                                {{ $activeTab === 'skills' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Skills
                    </a>
                    <a href="?tab=security"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors
                                {{ $activeTab === 'security' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Security
                    </a>
                    <a href="?tab=account"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-md text-center transition-colors
                                {{ $activeTab === 'account' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Account
                    </a>
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
                                    <select name="level"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required>
                                        <option value="1" selected>Novice</option>
                                        <option value="2">Beginner</option>
                                        <option value="3">Skilled</option>
                                        <option value="4">Expert</option>
                                    </select>
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
                                    @php
                                        $levelLabels = [1 => 'Novice', 2 => 'Beginner', 3 => 'Skilled', 4 => 'Expert'];
                                        $levelPercents = [1 => 25, 2 => 50, 3 => 75, 4 => 100];
                                        $levelColors = [
                                            1 => 'bg-gradient-to-r from-slate-400 to-slate-500',
                                            2 => 'bg-gradient-to-r from-blue-400 to-blue-500',
                                            3 => 'bg-gradient-to-r from-indigo-500 to-purple-500',
                                            4 => 'bg-gradient-to-r from-amber-400 to-orange-500'
                                        ];
                                        $levelLabel = $levelLabels[$skill->level] ?? 'Unknown';
                                        $levelPercent = $levelPercents[$skill->level] ?? 25;
                                        $levelColor = $levelColors[$skill->level] ?? 'bg-blue-500';
                                    @endphp
                                    <div class="group relative p-4 bg-slate-50 hover:bg-white rounded-xl border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                                        {{-- Hover Tooltip --}}
                                        <div class="absolute -top-10 left-4 px-3 py-1.5 bg-slate-900 text-white text-xs font-medium rounded-lg 
                                                    opacity-0 translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 
                                                    transition-all duration-200 ease-out pointer-events-none z-20 whitespace-nowrap
                                                    before:content-[''] before:absolute before:top-full before:left-4 before:border-4 before:border-transparent before:border-t-slate-900">
                                            Level: {{ $levelLabel }}
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-semibold text-slate-800">{{ $skill->tech_name }}</span>
                                                    <span class="text-xs font-medium text-slate-400">{{ $levelPercent }}%</span>
                                                </div>
                                                <div class="h-2.5 bg-slate-200 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-500 {{ $levelColor }}"
                                                        style="width: {{ $levelPercent }}%"></div>
                                                </div>
                                            </div>

                                            {{-- Level Dropdown --}}
                                            <form method="POST" action="{{ route('profile.skills.update', $skill->id) }}" class="shrink-0">
                                                @csrf
                                                @method('PUT')
                                                <select name="level" onchange="this.form.submit()"
                                                    class="px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer hover:border-blue-300 transition-colors">
                                                    <option value="1" {{ $skill->level === 1 ? 'selected' : '' }}>Novice</option>
                                                    <option value="2" {{ $skill->level === 2 ? 'selected' : '' }}>Beginner</option>
                                                    <option value="3" {{ $skill->level === 3 ? 'selected' : '' }}>Skilled</option>
                                                    <option value="4" {{ $skill->level === 4 ? 'selected' : '' }}>Expert</option>
                                                </select>
                                            </form>

                                            {{-- Delete Button --}}
                                            <form method="POST" action="{{ route('profile.skills.destroy', $skill->id) }}" class="shrink-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
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
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-slate-700 mb-1">
                                Username
                            </label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">@</span>
                                    <input type="text" name="username" id="username-input"
                                        value="{{ $user->username }}" data-original="{{ $user->username }}"
                                        class="w-full pl-8 pr-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        minlength="3" maxlength="20" pattern="[a-zA-Z0-9_]+">
                                </div>
                                <button type="button" id="btn-check-username"
                                    class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg border border-slate-300 hover:bg-slate-200 transition-colors whitespace-nowrap">
                                    Check
                                </button>
                            </div>

                            {{-- Status Message --}}
                            <div id="username-check-status" class="mt-2 hidden">
                                <div class="flex items-center gap-2">
                                    <span id="username-status-icon"></span>
                                    <span id="username-status-text" class="text-sm"></span>
                                </div>
                            </div>

                            <p class="text-xs text-slate-500 mt-2">Letters, numbers, and underscores only. 3-20 characters.</p>
                        </div>

                        {{-- Save Button --}}
                        <div class="flex justify-end">
                            <button type="button" id="btn-save-username" disabled
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Username
                            </button>
                        </div>
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

                {{-- Username Check/Save Script --}}
                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const input = document.getElementById('username-input');
                    const checkBtn = document.getElementById('btn-check-username');
                    const saveBtn = document.getElementById('btn-save-username');
                    const statusDiv = document.getElementById('username-check-status');
                    const statusIcon = document.getElementById('username-status-icon');
                    const statusText = document.getElementById('username-status-text');

                    if (!input || !checkBtn || !saveBtn) return;

                    let isAvailable = false;
                    const originalUsername = input.dataset.original;

                    // Check username availability
                    checkBtn.addEventListener('click', async () => {
                        const username = input.value.trim().toLowerCase();
                        
                        if (!username || username.length < 3) {
                            showStatus('error', 'Username must be at least 3 characters');
                            return;
                        }

                        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                            showStatus('error', 'Only letters, numbers, and underscores allowed');
                            return;
                        }

                        if (username === originalUsername) {
                            showStatus('info', 'This is your current username');
                            saveBtn.disabled = true;
                            return;
                        }

                        checkBtn.disabled = true;
                        checkBtn.textContent = 'Checking...';

                        try {
                            const response = await fetch(`/settings/username/check?username=${encodeURIComponent(username)}`);
                            const data = await response.json();

                            if (data.available) {
                                showStatus('success', 'Username is available!');
                                isAvailable = true;
                                saveBtn.disabled = false;
                            } else {
                                showStatus('error', data.reason || 'Username is taken');
                                isAvailable = false;
                                saveBtn.disabled = true;
                            }
                        } catch (e) {
                            showStatus('error', 'Failed to check username');
                        }

                        checkBtn.disabled = false;
                        checkBtn.textContent = 'Check';
                    });

                    // Save username
                    saveBtn.addEventListener('click', async () => {
                        if (!isAvailable) return;

                        const username = input.value.trim().toLowerCase();
                        saveBtn.disabled = true;
                        saveBtn.textContent = 'Saving...';

                        try {
                            const response = await fetch('/settings/username', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ username })
                            });

                            const data = await response.json();

                            if (data.success) {
                                showStatus('success', 'Username updated! Redirecting...');
                                setTimeout(() => {
                                    window.location.href = `/profile/settings?tab=account`;
                                }, 1000);
                            } else {
                                showStatus('error', data.message || 'Failed to update username');
                                saveBtn.disabled = false;
                            }
                        } catch (e) {
                            showStatus('error', 'Failed to save username');
                            saveBtn.disabled = false;
                        }

                        saveBtn.textContent = 'Save Username';
                    });

                    // Reset on input change
                    input.addEventListener('input', () => {
                        isAvailable = false;
                        saveBtn.disabled = true;
                        statusDiv.classList.add('hidden');
                    });

                    function showStatus(type, message) {
                        statusDiv.classList.remove('hidden');
                        statusText.textContent = message;
                        
                        if (type === 'success') {
                            statusIcon.innerHTML = '✓';
                            statusIcon.className = 'text-green-500';
                            statusText.className = 'text-sm text-green-600';
                        } else if (type === 'error') {
                            statusIcon.innerHTML = '✗';
                            statusIcon.className = 'text-red-500';
                            statusText.className = 'text-sm text-red-600';
                        } else {
                            statusIcon.innerHTML = 'ℹ';
                            statusIcon.className = 'text-blue-500';
                            statusText.className = 'text-sm text-blue-600';
                        }
                    }
                });
                </script>
            @endif

        </div>
    </div>
@endsection