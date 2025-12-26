@extends('layouts.app')

@section('title', 'Onboarding')

@section('content')
    <div class="min-h-screen bg-linear-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-900 mb-2">Selamat Datang, {{ $user->username }}!</h1>
                <p class="text-slate-600">Lengkapi profil Anda untuk memulai perjalanan belajar bersama di komunitas Java
                    Developer Group</p>
            </div>

            {{-- Global Error Display --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-red-800 mb-1">Ada kesalahan:</h4>
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Progress Indicator --}}
            <div class="mb-8">
                <div class="flex items-center justify-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium step-indicator active"
                            data-step="1">
                            1
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600 step-label active" data-step="1">Personal
                            Info</span>
                    </div>
                    <div class="w-16 h-1 bg-slate-200 progress-line" data-line="1"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-slate-200 text-slate-500 rounded-full flex items-center justify-center text-sm font-medium step-indicator"
                            data-step="2">
                            2
                        </div>
                        <span class="ml-2 text-sm font-medium text-slate-500 step-label" data-step="2">Preferensi</span>
                    </div>
                </div>
            </div>

            {{-- Onboarding Form --}}
            <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden"
                id="onboarding-root"
                @if ($errors->has('preferred_role') || $errors->has('focus_areas') || $errors->has('submit'))
                    data-start-step="2"
                @endif
            >
                <form method="POST" action="{{ route('onboarding.store') }}" id="onboarding-form" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Step 1: Personal Info --}}
                    <div class="onboarding-step active" id="step-1">
                        <x-onboarding.step-personal :user="$user" />
                    </div>

                    {{-- Step 2: Preferences --}}
                    <div class="onboarding-step hidden" id="step-2">
                        <x-onboarding.step-focus />
                        <x-onboarding.step-role />
                    </div>

                    {{-- Navigation --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <div class="flex justify-between">
                            <button type="button" id="prev-btn"
                                class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors hidden">
                                ← Sebelumnya
                            </button>
                            <div class="flex-1"></div>
                            <button type="button" id="next-btn"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Selanjutnya →
                            </button>
                            <button type="submit" id="submit-btn"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed hidden">
                                <span class="submit-text">Selesai ✓</span>
                                <span class="submit-loading hidden">
                                    <svg class="animate-spin h-5 w-5 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Help Text --}}
            <div class="text-center mt-6">
                <p class="text-sm text-slate-500">
                    Butuh bantuan? <a href="#" class="text-blue-600 hover:text-blue-700">Hubungi support</a>
                </p>
            </div>
        </div>
    </div>
@endsection