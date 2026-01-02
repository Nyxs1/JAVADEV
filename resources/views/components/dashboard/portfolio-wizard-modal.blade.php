@props(['userCourses' => collect(), 'portfolio' => null])

@php
    $isEdit = !is_null($portfolio);
    $portfolioData = $portfolio ? [
        'id' => $portfolio->id,
        'title' => $portfolio->title,
        'description' => $portfolio->description,
        'readme_md' => $portfolio->readme_md,
        'source_course_id' => $portfolio->source_id,
        'cover_url' => $portfolio->cover_url,
        'screenshots' => $portfolio->screenshots->map(fn($s) => [
            'id' => $s->id,
            'url' => $s->url,
        ])->toArray(),
        'evidences' => $portfolio->evidences->map(fn($e) => [
            'id' => $e->id,
            'type' => $e->type,
            'label' => $e->label,
            'value' => $e->value,
        ])->toArray(),
    ] : null;
@endphp

<div x-data="portfolioWizard({{ json_encode($portfolioData) }}, {{ json_encode($userCourses->toArray()) }})"
    x-show="isOpen" x-cloak @keydown.escape.window="close()" @javadev:portfolio:open.window="open($event.detail)"
    @open-portfolio-wizard.window="open($event.detail)" class="fixed inset-0 z-50">

    {{-- Backdrop with smooth fade --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" x-show="isOpen"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="close()">
    </div>

    {{-- Modal Panel --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
            x-show="isOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4" @click.stop>

            {{-- Header --}}
            <div
                class="p-5 border-b border-slate-200 flex items-center justify-between shrink-0 bg-gradient-to-r from-blue-50 to-slate-50">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900"
                        x-text="isEditMode ? 'Edit Portfolio' : 'Tambah Portfolio'"></h2>
                    <p class="text-sm text-slate-500 mt-0.5">Step <span x-text="currentStep"></span> of 5</p>
                </div>
                <button type="button" @click="close()"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-all duration-150 hover:scale-105 active:scale-95"
                    :disabled="isSubmitting">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Step Indicators with animation --}}
            <div
                class="px-5 py-3 border-b border-slate-100 flex items-center gap-2 shrink-0 overflow-x-auto bg-slate-50/50">
                <template x-for="step in 5" :key="step">
                    <button type="button" @click="goToStep(step)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all duration-200 transform hover:scale-105 active:scale-95"
                        :class="{
                            'bg-blue-500 text-white shadow-md shadow-blue-200': currentStep === step,
                            'bg-green-100 text-green-700': currentStep > step,
                            'bg-slate-100 text-slate-500 hover:bg-slate-200': currentStep < step
                        }" :disabled="step > currentStep + 1 || isSubmitting">
                        <span class="flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold"
                            :class="{
                                'bg-white/20': currentStep === step,
                                'bg-green-200': currentStep > step,
                                'bg-slate-200': currentStep < step
                            }" x-text="step"></span>
                        <span x-text="['Basic', 'Media', 'README', 'Evidence', 'Publish'][step - 1]"></span>
                    </button>
                </template>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('dashboard.portfolio.upsert') }}" enctype="multipart/form-data"
                class="flex-1 overflow-y-auto" @submit="handleSubmit($event)">
                @csrf
                <input type="hidden" name="portfolio_id" :value="portfolioId">
                <input type="hidden" name="publish_now" :value="publishNow ? 1 : 0">

                {{-- Hidden inputs for evidence (submitted with form) --}}
                <template x-for="(ev, idx) in newEvidences" :key="'new-ev-' + idx">
                    <div>
                        <input type="hidden" :name="'new_evidences[' + idx + '][type]'" :value="ev.type">
                        <input type="hidden" :name="'new_evidences[' + idx + '][label]'" :value="ev.label">
                        <input type="hidden" :name="'new_evidences[' + idx + '][value]'" :value="ev.value">
                    </div>
                </template>

                {{-- Step Container with transitions --}}
                <div class="p-5 relative min-h-[300px]">

                    {{-- Step 1: Basic --}}
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Judul Project <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" x-model="form.title" required maxlength="255"
                                    x-ref="titleInput"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                                    placeholder="Contoh: Website E-commerce">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Singkat</label>
                                <textarea name="description" x-model="form.description" rows="3" maxlength="2000"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all duration-150"
                                    placeholder="Ceritakan singkat tentang project ini..."></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Sumber Project</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" x-model="sourceType" value="manual"
                                            class="w-4 h-4 text-blue-600 transition-all">
                                        <span
                                            class="text-sm text-slate-700 group-hover:text-blue-600 transition-colors">Manual
                                            / Pribadi</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" x-model="sourceType" value="course"
                                            class="w-4 h-4 text-blue-600 transition-all">
                                        <span
                                            class="text-sm text-slate-700 group-hover:text-blue-600 transition-colors">Built
                                            from Course</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="sourceType === 'course'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="pl-6 border-l-2 border-blue-200">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Course</label>
                                <select name="source_course_id" x-model="form.source_course_id"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150">
                                    <option value="">— Pilih course —</option>
                                    <template x-for="course in courses" :key="course.id">
                                        <option :value="course.id" x-text="course.course_name || course.course_id">
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Media --}}
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Cover Image</label>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-32 h-20 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center border-2 border-dashed border-slate-300 transition-all duration-200 hover:border-blue-400">
                                        <template x-if="coverPreview">
                                            <img :src="coverPreview" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!coverPreview">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </template>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="cover" accept="image/*"
                                            @change="handleCoverChange($event)"
                                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:transition-all file:duration-150 file:cursor-pointer">
                                        <p class="text-xs text-slate-500 mt-1">Max 2MB. Recommended 16:9.</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Screenshots</label>
                                <div class="grid grid-cols-4 gap-2 mb-3"
                                    x-show="existingScreenshots.length > 0 || newScreenshotPreviews.length > 0">
                                    <template x-for="(ss, idx) in existingScreenshots" :key="'ex-' + ss.id">
                                        <div
                                            class="relative group aspect-video rounded-xl overflow-hidden border border-slate-200 bg-slate-100 transition-all duration-150 hover:shadow-md">
                                            <img :src="ss.url" class="w-full h-full object-cover">
                                            <button type="button" @click="deleteScreenshot(ss.id, idx)"
                                                class="absolute top-1 right-1 p-1.5 bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-150 hover:bg-red-600 hover:scale-110">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-for="(preview, idx) in newScreenshotPreviews" :key="'new-' + idx">
                                        <div
                                            class="relative aspect-video rounded-xl overflow-hidden border-2 border-blue-200 bg-blue-50">
                                            <img :src="preview" class="w-full h-full object-cover">
                                            <span
                                                class="absolute top-1 left-1 px-2 py-0.5 text-xs bg-blue-500 text-white rounded-lg font-medium">New</span>
                                        </div>
                                    </template>
                                </div>
                                <input type="file" name="screenshots[]" accept="image/*" multiple
                                    @change="handleScreenshotsChange($event)"
                                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:transition-all file:duration-150 file:cursor-pointer">
                                <p class="text-xs text-slate-500 mt-1">Max 4MB per file.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: README --}}
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">README (Markdown)</label>
                            <p class="text-xs text-slate-500 mb-2">Tulis seperti README.md di GitHub.</p>
                            <textarea name="readme_md" x-model="form.readme_md" rows="14"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm resize-none transition-all duration-150"
                                placeholder="## Overview
Jelaskan project ini...

## Tech Stack
- Laravel
- Alpine.js

## Features
- Fitur 1
- Fitur 2"></textarea>
                        </div>
                    </div>

                    {{-- Step 4: Evidence (Inline) --}}
                    <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-slate-700">Evidence Links</label>
                                <button type="button" @click="addNewEvidence()"
                                    class="px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-150 hover:scale-105 active:scale-95">
                                    + Tambah Evidence
                                </button>
                            </div>
                            <p class="text-xs text-slate-500">Link ke repository, demo, atau dokumentasi.</p>

                            {{-- Existing Evidence (from DB) --}}
                            <template x-for="(evidence, idx) in existingEvidences" :key="'existing-' + evidence.id">
                                <div
                                    class="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200 transition-all duration-150 hover:shadow-sm">
                                    <span class="px-2 py-1 text-xs bg-slate-200 rounded-lg font-medium"
                                        x-text="evidence.type"></span>
                                    <span class="flex-1 text-sm truncate"
                                        x-text="evidence.label || evidence.value"></span>
                                    <a :href="evidence.value" target="_blank"
                                        class="text-blue-600 hover:underline text-xs">Open</a>
                                    <button type="button" @click="deleteExistingEvidence(evidence.id, idx)"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-150 hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            {{-- New Evidence (inline form) --}}
                            <template x-for="(ev, idx) in newEvidences" :key="'new-' + idx">
                                <div class="flex items-start gap-2 p-3 bg-blue-50 border border-blue-100 rounded-xl transition-all duration-150"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100">
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <select x-model="ev.type"
                                            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                                            <option value="github">GitHub</option>
                                            <option value="link">Link</option>
                                            <option value="demo">Demo</option>
                                            <option value="pdf">PDF</option>
                                        </select>
                                        <input type="text" x-model="ev.label" placeholder="Label (optional)"
                                            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                                        <input type="url" x-model="ev.value" placeholder="https://..." required
                                            class="px-3 py-2 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 transition-all duration-150">
                                    </div>
                                    <button type="button" @click="removeNewEvidence(idx)"
                                        class="p-2 text-red-500 hover:bg-red-100 rounded-xl transition-all duration-150 hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <div x-show="existingEvidences.length === 0 && newEvidences.length === 0"
                                class="text-center py-8 text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                <p class="text-sm">Belum ada evidence. Klik "+ Tambah Evidence" di atas.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 5: Publish --}}
                    <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-4">
                        <div class="space-y-5">
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <h4 class="font-medium text-amber-800">Sebelum Publish</h4>
                                        <ul class="mt-2 text-sm text-amber-700 space-y-1 list-disc list-inside">
                                            <li>Portfolio akan terlihat di <strong>profil publik</strong> kamu</li>
                                            <li>Pastikan tidak ada <strong>data sensitif</strong></li>
                                            <li>Semua link harus <strong>aman dan aktif</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                <h4 class="font-medium text-slate-700 mb-3">Ringkasan</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex">
                                        <dt class="w-24 text-slate-500">Judul:</dt>
                                        <dd class="text-slate-900 font-medium" x-text="form.title || '-'"></dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-24 text-slate-500">Sumber:</dt>
                                        <dd class="text-slate-900"
                                            x-text="sourceType === 'course' ? 'Built from Course' : 'Manual'"></dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-24 text-slate-500">Cover:</dt>
                                        <dd class="text-slate-900" x-text="coverPreview ? 'Ya' : 'Tidak'"></dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="w-24 text-slate-500">Evidence:</dt>
                                        <dd class="text-slate-900"
                                            x-text="(existingEvidences.length + newEvidences.length) + ' link'"></dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Agreement Checkbox --}}
                            <label
                                class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                :class="agreePublish ? 'border-blue-400 bg-blue-50 shadow-md shadow-blue-100' : 'border-slate-200 hover:bg-slate-50 hover:border-slate-300'">
                                <input type="checkbox" name="agree_publish" x-model="agreePublish" value="1"
                                    class="w-5 h-5 mt-0.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-all">
                                <div>
                                    <span class="font-medium text-slate-900">Aku paham dan setuju</span>
                                    <p class="text-sm text-slate-500 mt-0.5">Saya telah memeriksa konten dan bertanggung
                                        jawab atas apa yang saya publikasikan.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer with micro-interactions --}}
                <div
                    class="p-5 border-t border-slate-200 flex items-center justify-between shrink-0 bg-gradient-to-r from-slate-50 to-blue-50/30">
                    <button type="button" @click="previousStep()" x-show="currentStep > 1" :disabled="isSubmitting"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-xl transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                        ← Kembali
                    </button>
                    <div x-show="currentStep === 1"></div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="nextStep()" x-show="currentStep < 5" :disabled="isSubmitting"
                            class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-blue-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Lanjut →
                        </button>

                        <template x-if="currentStep === 5">
                            <div class="flex items-center gap-3">
                                <button type="submit" @click="publishNow = false" :disabled="isSubmitting"
                                    class="px-4 py-2.5 text-sm font-medium text-slate-700 border border-slate-300 rounded-xl hover:bg-slate-100 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <template x-if="isSubmitting && !publishNow">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                    <span x-text="isSubmitting && !publishNow ? 'Menyimpan...' : 'Simpan Draft'"></span>
                                </button>
                                <button type="submit" @click="publishNow = true"
                                    :disabled="!agreePublish || isSubmitting"
                                    class="px-6 py-2.5 text-sm font-medium text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-green-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <template x-if="isSubmitting && publishNow">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                    <span x-text="isSubmitting && publishNow ? 'Publishing...' : 'Publish'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('portfolioWizard', (initialData, courses) => ({
            isOpen: false,
            isEditMode: false,
            isSubmitting: false,
            currentStep: 1,
            portfolioId: null,
            publishNow: false,
            agreePublish: false,
            sourceType: 'manual',
            courses: courses || [],

            form: {
                title: '',
                description: '',
                readme_md: '',
                source_course_id: '',
            },

            coverPreview: null,
            existingScreenshots: [],
            newScreenshotPreviews: [],
            existingEvidences: [],
            newEvidences: [],

            init() {
                // Event listeners handled via x-on.window on root element
            },

            open(data = null) {
                this.reset();

                if (data && data.id) {
                    this.isEditMode = true;
                    this.portfolioId = data.id;
                    this.form.title = data.title || '';
                    this.form.description = data.description || '';
                    this.form.readme_md = data.readme_md || '';
                    this.form.source_course_id = data.source_course_id || '';
                    this.coverPreview = data.cover_url || null;
                    this.existingScreenshots = data.screenshots || [];
                    this.existingEvidences = data.evidences || [];
                    this.sourceType = data.source_course_id ? 'course' : 'manual';

                    if (data.goToPublish) {
                        this.currentStep = 5;
                    }
                }

                this.isOpen = true;
                document.body.style.overflow = 'hidden';

                // Focus title input after modal opens
                this.$nextTick(() => {
                    if (this.$refs.titleInput && this.currentStep === 1) {
                        this.$refs.titleInput.focus();
                    }
                });
            },

            close() {
                if (this.isSubmitting) return; // Prevent close during submit

                this.isOpen = false;
                document.body.style.overflow = '';

                // Dispatch close event
                window.dispatchEvent(new CustomEvent('javadev:portfolio:close'));
            },

            reset() {
                this.isEditMode = false;
                this.isSubmitting = false;
                this.currentStep = 1;
                this.portfolioId = null;
                this.publishNow = false;
                this.agreePublish = false;
                this.sourceType = 'manual';
                this.form = { title: '', description: '', readme_md: '', source_course_id: '' };
                this.coverPreview = null;
                this.existingScreenshots = [];
                this.newScreenshotPreviews = [];
                this.existingEvidences = [];
                this.newEvidences = [];
            },

            goToStep(step) {
                if (this.isSubmitting) return;

                // Validate current step before moving forward
                if (step > this.currentStep) {
                    if (!this.validateCurrentStep()) return;
                }

                if (step <= this.currentStep + 1 && step >= 1 && step <= 5) {
                    this.currentStep = step;

                    // Dispatch step change event
                    window.dispatchEvent(new CustomEvent('javadev:portfolio:step-change', {
                        detail: { step, total: 5 }
                    }));
                }
            },

            validateCurrentStep() {
                if (this.currentStep === 1 && !this.form.title.trim()) {
                    // Show inline validation instead of alert
                    const titleInput = this.$refs.titleInput;
                    if (titleInput) {
                        titleInput.focus();
                        titleInput.classList.add('border-red-500', 'ring-2', 'ring-red-200');
                        setTimeout(() => {
                            titleInput.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
                        }, 2000);
                    }
                    return false;
                }
                return true;
            },

            nextStep() {
                if (this.isSubmitting) return;

                if (this.currentStep < 5) {
                    if (!this.validateCurrentStep()) return;
                    this.currentStep++;

                    // Dispatch step change event
                    window.dispatchEvent(new CustomEvent('javadev:portfolio:step-change', {
                        detail: { step: this.currentStep, total: 5 }
                    }));
                }
            },

            previousStep() {
                if (this.isSubmitting) return;
                if (this.currentStep > 1) this.currentStep--;
            },

            handleCoverChange(event) {
                const file = event.target.files[0];
                if (file) this.coverPreview = URL.createObjectURL(file);
            },

            handleScreenshotsChange(event) {
                this.newScreenshotPreviews = [];
                for (let f of event.target.files) {
                    this.newScreenshotPreviews.push(URL.createObjectURL(f));
                }
            },

            async deleteScreenshot(id, index) {
                if (!confirm('Hapus screenshot ini?')) return;
                try {
                    const resp = await fetch(`/dashboard/portfolio-screenshots/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    if (resp.ok) this.existingScreenshots.splice(index, 1);
                } catch (e) { console.error(e); }
            },

            async deleteExistingEvidence(id, index) {
                if (!confirm('Hapus evidence ini?')) return;
                try {
                    const resp = await fetch(`/dashboard/evidence/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    if (resp.ok) this.existingEvidences.splice(index, 1);
                } catch (e) { console.error(e); }
            },

            addNewEvidence() {
                this.newEvidences.push({ type: 'github', label: '', value: '' });
            },

            removeNewEvidence(idx) {
                this.newEvidences.splice(idx, 1);
            },

            handleSubmit(event) {
                if (this.currentStep !== 5) {
                    event.preventDefault();
                    this.nextStep();
                    return false;
                }

                // Show loading state
                this.isSubmitting = true;

                // Form will submit naturally
                return true;
            }
        }));
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>