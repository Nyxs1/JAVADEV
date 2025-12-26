@extends('layouts.app')

@section('title', $user->username . ' - Profile')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @php
                $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
                $displayUsername = ltrim($user->username, '@');
            @endphp

            {{-- VERTICAL LAYOUT: All cards stacked --}}
            <div class="flex flex-col gap-6">

                {{-- ======================================= --}}
                {{-- CARD 1: COVER PHOTO (Horizontal Banner) --}}
                {{-- NO avatar bubble, NO edit button here --}}
                {{-- ======================================= --}}
                <div>
                    <x-profile.photo-frame 
                        :src="$hasAvatar ? $user->avatar_url : null"
                        :alt="$user->full_name ?: $displayUsername"
                        :fallback-initial="substr($displayUsername, 0, 1)"
                        :show-editor="false"
                    />
                </div>

                {{-- ======================================= --}}
                {{-- CARD 2: IDENTITY --}}
                {{-- ======================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden py-6 px-6 text-center">
                    {{-- Username --}}
                    <h1 class="text-2xl font-bold text-slate-900">{{ $displayUsername }}</h1>
                    
                    {{-- Full Name --}}
                    @if($user->full_name)
                        <p class="text-slate-500 mt-1">{{ $user->full_name }}</p>
                    @endif

                    {{-- Role Badge --}}
                    <div class="mt-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            {{ $user->role ? ucfirst($user->role->name) : 'Member' }}
                        </span>
                    </div>

                    {{-- Bio --}}
                    @if($user->bio)
                        <p class="text-slate-600 text-sm mt-4 leading-relaxed max-w-md mx-auto">{{ $user->bio }}</p>
                    @else
                        <p class="text-slate-400 italic text-sm mt-4">No bio added yet</p>
                    @endif

                    {{-- Tech Stack (Display Only as Chips) --}}
                    @if($user->skills->count() > 0)
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach($user->skills as $skill)
                                    <span class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200 hover:border-blue-300 transition-colors" title="{{ $skill->level_label }}">
                                        {{ $skill->tech_name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- NO Edit Button here - Settings is the entry point --}}
                </div>

                {{-- ======================================= --}}
                {{-- CARD 3: INFORMATION --}}
                {{-- ======================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-5">Information</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Email --}}
                        @if($user->email)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-slate-400 uppercase">Email</p>
                                    <p class="text-sm text-slate-800 font-medium truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Age --}}
                        @if($user->birth_date)
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase">Age</p>
                                    <p class="text-sm text-slate-800 font-medium">{{ $user->age }} y/o <span class="text-slate-400 text-xs">({{ $user->birth_date->format('M Y') }})</span></p>
                                </div>
                            </div>
                        @endif

                        {{-- Community Stats --}}
                        <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">Events</p>
                                <p class="text-sm text-slate-800 font-medium">{{ $eventSummary['total'] }} Participated</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================================= --}}
                {{-- CARD 4: HISTORY (Tabs) --}}
                {{-- ======================================= --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <x-profile.public-tabs :user="$user" :is-own-profile="$isOwnProfile"
                        :portfolio-activities="$portfolioActivities" :course-activities="$courseActivities"
                        :discussion-summary="$discussionSummary" />
                </div>

            </div>

        </div>
    </div>
@endsection