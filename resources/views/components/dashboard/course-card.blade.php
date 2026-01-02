@props(['course'])

@php
    $isPublished = $course->is_published;
    $isCompleted = $course->status === 'completed';
    $isInProgress = $course->status === 'in_progress';
    $progressColor = $isCompleted ? 'bg-green-500' : 'bg-blue-500';
    $evidences = $course->evidences ?? collect();
@endphp

<div class="course-card p-4 border border-slate-200 rounded-xl hover:border-slate-300 transition-all bg-white hover:shadow-md"
    data-course-id="{{ $course->id }}" data-status="{{ $course->status }}">

    <div class="flex items-start gap-4">
        {{-- Course Icon --}}
        <div class="shrink-0 w-14 h-14 rounded-lg flex items-center justify-center
            {{ $isCompleted ? 'bg-green-100' : 'bg-blue-100' }}">
            <svg class="w-7 h-7 {{ $isCompleted ? 'text-green-600' : 'text-blue-600' }}" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>

        {{-- Course Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div>
                    <h3 class="font-semibold text-slate-900 line-clamp-1">
                        {{ $course->course_name ?: $course->course_id }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $isCompleted ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $course->status_label }}
                        </span>
                        <span
                            class="course-status-badge px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $isPublished ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="flex items-center gap-3 mt-3">
                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full {{ $progressColor }} rounded-full transition-all"
                        style="width: {{ $course->progress_percent }}%"></div>
                </div>
                <span class="text-xs font-medium text-slate-600 shrink-0">{{ $course->progress_percent }}%</span>
            </div>

            {{-- Evidence Section --}}
            @if($evidences->count() > 0)
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-slate-500">Evidence:</span>
                        @foreach($evidences->take(3) as $evidence)
                            <a href="{{ $evidence->value }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-md transition-colors"
                                title="{{ $evidence->display_label }}">
                                @if($evidence->type === 'github')
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                                    </svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                @endif
                                <span>{{ Str::limit($evidence->display_label, 15) }}</span>
                            </a>
                        @endforeach
                        @if($evidences->count() > 3)
                            <span class="text-xs text-slate-400">+{{ $evidences->count() - 3 }} more</span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-400">
                    @if($course->completed_at)
                        Selesai {{ $course->completed_at->format('d M Y') }}
                    @elseif($course->last_activity_at)
                        Terakhir belajar {{ $course->last_activity_at->diffForHumans() }}
                    @else
                        Ditambahkan {{ $course->created_at->diffForHumans() }}
                    @endif
                </span>
                <div class="flex items-center gap-2">
                    {{-- Add Evidence Button --}}
                    <button type="button" data-open-evidence-modal data-item-type="user_course"
                        data-item-id="{{ $course->id }}"
                        class="p-1.5 text-slate-400 hover:text-blue-600 transition-all duration-150 hover:scale-110"
                        title="Tambah Evidence">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </button>

                    {{-- Publish/Unpublish Button --}}
                    @if($isPublished)
                        <form method="POST" action="{{ route('dashboard.courses.unpublish', $course) }}"
                            class="course-publish-form">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
                                Unpublish
                            </button>
                        </form>
                    @else
                        @if($isInProgress)
                            {{-- Show warning modal for in-progress courses --}}
                            <button type="button" data-show-publish-warning data-course-id="{{ $course->id }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] shadow-sm">
                                Publish
                            </button>
                        @else
                            <form method="POST" action="{{ route('dashboard.courses.publish', $course) }}"
                                class="course-publish-form">
                                @csrf
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] shadow-sm">
                                    Publish
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Publish Warning Modal (hidden by default) --}}
    @if($isInProgress && !$isPublished)
        <div id="publish-warning-{{ $course->id }}" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50" data-hide-publish-warning data-course-id="{{ $course->id }}"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Publish course ini?</h3>
                        </div>
                        <p class="text-sm text-slate-600 mb-6">
                            Course ini belum selesai. Kamu tetap bisa publish kalau sudah punya bukti (repo/link).
                            Lanjutkan?
                        </p>
                        <div class="flex justify-end gap-3">
                            <button type="button" data-hide-publish-warning data-course-id="{{ $course->id }}"
                                class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('dashboard.courses.publish', $course) }}">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                    Lanjutkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>