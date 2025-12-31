{{-- Portfolio Tab (Dashboard) --}}
@php
    $isOwner = auth()->check() && auth()->id() === $user->id;
@endphp
<div class="space-y-6">
    {{-- Header with Add Button --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">My Portfolio</h1>
                <p class="text-slate-600 mt-1">Manage your projects and showcase your work</p>
            </div>
            @if($isOwner)
                <button type="button" onclick="openPortfolioWizard()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Portfolio</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    {{-- Portfolio Content --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        @if(isset($portfolios) && $portfolios->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($portfolios as $portfolio)
                        <x-dashboard.portfolio-card :portfolio="$portfolio" />
                    @endforeach
                </div>
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-slate-500">Belum ada portfolio</p>
                @if($isOwner)
                    <p class="text-sm text-slate-400 mt-1">Klik tombol "Tambah Portfolio" untuk memulai</p>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Evidence Modal --}}
@if($isOwner)
    <div id="evidence-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeEvidenceModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Tambah Evidence</h2>
                    <button type="button" onclick="closeEvidenceModal()"
                        class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
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
                        <select name="type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            <option value="github">GitHub</option>
                            <option value="link">Link</option>
                            <option value="demo">Demo</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Label (optional)</label>
                        <input type="text" name="label" maxlength="100"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="e.g., Main Repository">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">URL <span
                                class="text-red-500">*</span></label>
                        <input type="url" name="value" required maxlength="500"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg"
                            placeholder="https://github.com/...">
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" onclick="closeEvidenceModal()"
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Portfolio Wizard Modal --}}
    <x-dashboard.portfolio-wizard-modal :userCourses="$userCourses ?? collect()" />

    <script>
        function openEvidenceModal(itemType, itemId) {
            document.getElementById('evidence-item-type').value = itemType;
            document.getElementById('evidence-item-id').value = itemId;
            document.getElementById('evidence-modal').classList.remove('hidden');
        }

        function closeEvidenceModal() {
            document.getElementById('evidence-modal').classList.add('hidden');
            document.getElementById('evidence-form').reset();
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeEvidenceModal();
        });
    </script>
@endif