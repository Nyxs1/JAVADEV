@extends('layouts.app')

@section('title', 'My Portfolio')

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
                <h1 class="text-2xl font-bold text-slate-900">Portfolio</h1>
                <p class="text-slate-600 mt-1">Kelola portfolio project-mu.</p>
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
                            <strong>Tips:</strong> Yang kamu publish akan tampil di profil publik.
                            Item dengan status "Draft" hanya terlihat olehmu.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            <x-ui.flash-message type="success" :message="session('success')" />
            <x-ui.flash-message type="error" :message="session('error')" />

            {{-- Create New Portfolio Button --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Portfolio Kamu</h2>
                    <p class="text-sm text-slate-500">{{ $portfolios->count() }} portfolio</p>
                </div>
                <button type="button" onclick="openPortfolioWizard()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Portfolio</span>
                </button>
            </div>

            {{-- Portfolio List --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-lg font-semibold text-slate-900">Portfolio Kamu</h2>
                </div>
                <div class="p-6">
                    @if($portfolios->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($portfolios as $portfolio)
                                <x-dashboard.portfolio-card :portfolio="$portfolio" />
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-slate-900 mb-2">Belum Ada Portfolio</h3>
                            <p class="text-slate-600 max-w-sm mx-auto">
                                Mulai tambahkan project-mu untuk ditampilkan di profil publik.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Evidence Add Modal --}}
    <div id="evidence-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeEvidenceModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Tambah Evidence</h2>
                    <button type="button" onclick="closeEvidenceModal()"
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
                        <button type="button" onclick="closeEvidenceModal()"
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

    <script>
        // Evidence modal functions
        function openEvidenceModal(itemType, itemId) {
            document.getElementById('evidence-item-type').value = itemType;
            document.getElementById('evidence-item-id').value = itemId;
            document.getElementById('evidence-modal').classList.remove('hidden');
        }

        function closeEvidenceModal() {
            document.getElementById('evidence-modal').classList.add('hidden');
            document.getElementById('evidence-form').reset();
        }

        // Close modal on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeEvidenceModal();
            }
        });
    </script>

    {{-- Portfolio Wizard Modal --}}
    <x-dashboard.portfolio-wizard-modal :userCourses="$userCourses" />
@endsection