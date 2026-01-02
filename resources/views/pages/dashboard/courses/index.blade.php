@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('users.dashboard', auth()->user()->username) }}"
                    class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back to Dashboard</span>
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Courses</h1>
                <p class="text-slate-600 mt-1">Kelola course yang kamu ikuti.</p>
            </div>

            {{-- Info Banner --}}
            <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-blue-800">
                            <strong>Tips:</strong> Publish course yang ingin kamu tampilkan di profil publik.
                            Progress belajarmu bisa jadi inspirasi buat developer lain!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            <x-ui.flash-message type="success" :message="session('success')" />
            <x-ui.flash-message type="error" :message="session('error')" />

            {{-- Add Demo Course (for testing) --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Tambah Course (Demo)</h2>
                    <p class="text-sm text-slate-500 mt-1">Untuk testing. Di produksi, course akan ditambahkan otomatis saat
                        kamu mengikuti course.</p>
                </div>
                <form method="POST" action="{{ route('dashboard.courses.store') }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label for="course_name" class="block text-sm font-medium text-slate-700 mb-1">
                                Nama Course <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="course_name" id="course_name"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: Laravel Fundamentals" required maxlength="255">
                        </div>
                        <div>
                            <label for="progress_percent" class="block text-sm font-medium text-slate-700 mb-1">
                                Progress (%)
                            </label>
                            <input type="number" name="progress_percent" id="progress_percent"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0-100" min="0" max="100" value="0">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Tambah Course
                        </button>
                    </div>
                </form>
            </div>

            {{-- Courses List --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Course Kamu</h2>
                </div>
                <div class="p-6">
                    @if($courses->count() > 0)
                        <div class="space-y-4">
                            @foreach($courses as $course)
                                <x-dashboard.course-card :course="$course" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 mb-2">Belum Ada Course</h3>
                            <p class="text-slate-600 max-w-sm mx-auto">
                                Mulai ikuti course untuk menampilkan progress belajarmu di profil publik.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Evidence Add Modal --}}
    <div id="evidence-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" data-close-evidence-overlay></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Tambah Evidence</h2>
                    <button type="button" data-close-evidence-modal
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="evidence-form" method="POST" action="{{ route('dashboard.evidence.store') }}"
                    class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="item_type" id="evidence-item-type">
                    <input type="hidden" name="item_id" id="evidence-item-id">

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="github">GitHub</option>
                            <option value="link">Link</option>
                            <option value="demo">Demo</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Label (optional)</label>
                        <input type="text" name="label" maxlength="100"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Main Repository">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">URL <span
                                class="text-red-500">*</span></label>
                        <input type="url" name="value" required maxlength="500"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="https://github.com/user/repo">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" data-close-evidence-modal
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/dashboard/portfolio.js')
@endpush