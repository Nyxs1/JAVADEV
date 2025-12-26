@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Sidebar --}}
                <aside class="lg:w-64 shrink-0">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden sticky top-24">
                        {{-- User Info --}}
                        <div class="p-4 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt=""
                                        class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-900 truncate">{{ $user->username }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->role?->name ?? 'Member' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <nav class="p-2">
                            {{-- Quick Links --}}
                            <div class="mb-2">
                                <p class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Quick
                                    Links</p>
                                <a href="{{ route('profile.show', $user->username) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors text-slate-700 hover:bg-slate-50">
                                    <img src="{{ asset('assets/icons/user-icon.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Public Profile</span>
                                </a>
                                <a href="{{ route('profile.settings') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors text-slate-700 hover:bg-slate-50">
                                    <img src="{{ asset('assets/icons/settings.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Settings</span>
                                </a>
                            </div>

                            {{-- Dashboard Tabs --}}
                            <div class="mb-2 pt-2 border-t border-slate-100">
                                <p class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">Dashboard
                                </p>
                                <a href="{{ route('users.dashboard', $user->username) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'overview' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <img src="{{ asset('assets/icons/home.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Overview</span>
                                </a>
                                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'events']) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'events' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Events</span>
                                </a>
                                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'portfolio']) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'portfolio' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Portfolio</span>
                                </a>
                                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'courses']) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'courses' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <img src="{{ asset('assets/icons/book.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                    <span>Courses</span>
                                </a>
                                <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'discussions']) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'discussions' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <img src="{{ asset('assets/icons/message-circle.svg') }}" alt=""
                                        class="w-5 h-5 opacity-60">
                                    <span>Discussions</span>
                                </a>

                                @if($isMentor)
                                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'mentor']) }}"
                                        class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'mentor' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                        <img src="{{ asset('assets/icons/users.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                        <span>Mentor Panel</span>
                                    </a>
                                @endif

                                @if($isAdmin)
                                    <a href="{{ route('users.dashboard', ['username' => $user->username, 'tab' => 'admin']) }}"
                                        class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ $tab === 'admin' ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                        <img src="{{ asset('assets/icons/shield.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                        <span>Admin Panel</span>
                                    </a>
                                    <a href="{{ route('index') }}"
                                        class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                        <img src="{{ asset('assets/icons/award.svg') }}" alt="" class="w-5 h-5 opacity-60">
                                        <span>Mentorship</span>
                                    </a>
                                @endif
                            </div>
                        </nav>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="flex-1 min-w-0">
                    @if($tab === 'overview')
                        @include('pages.users.partials.panels.overview')
                    @elseif($tab === 'events')
                        @include('pages.users.partials.panels.events')
                    @elseif($tab === 'portfolio')
                        @include('pages.users.partials.panels.portfolio')
                    @elseif($tab === 'courses')
                        @include('pages.users.partials.panels.courses')
                    @elseif($tab === 'discussions')
                        @include('pages.users.partials.panels.discussions')
                    @elseif($tab === 'mentor' && $isMentor)
                        @include('pages.users.partials.panels.mentor')
                    @elseif($tab === 'admin' && $isAdmin)
                        @include('pages.users.partials.panels.admin')
                    @endif
                </main>
            </div>
        </div>
    </div>
@endsection