@props(['portfolio'])

@php
    $isPublished = $portfolio->is_published;
    $isBuiltFromCourse = $portfolio->isBuiltFromCourse();
    $evidences = $portfolio->evidences ?? collect();
    $sourceCourse = $portfolio->builtFromCourse;
    $screenshots = $portfolio->screenshots ?? collect();

    // Prepare data for wizard
    $wizardData = [
        'id' => $portfolio->id,
        'title' => $portfolio->title,
        'description' => $portfolio->description,
        'readme_md' => $portfolio->readme_md,
        'source_course_id' => $portfolio->source_id,
        'cover_url' => $portfolio->cover_url,
        'screenshots' => $screenshots->map(fn($s) => [
            'id' => $s->id,
            'url' => $s->url,
        ])->toArray(),
        'evidences' => $evidences->map(fn($e) => [
            'id' => $e->id,
            'type' => $e->type,
            'label' => $e->label,
            'value' => $e->value,
        ])->toArray(),
    ];
@endphp

<div class="portfolio-card p-4 border border-slate-200 rounded-xl hover:border-slate-300 transition-all bg-white hover:shadow-md"
    data-portfolio-id="{{ $portfolio->id }}" data-portfolio-json='@json($wizardData)'>

    {{-- Cover Image --}}
    @if($portfolio->cover_path)
        <div class="mb-3 rounded-lg overflow-hidden bg-slate-100 aspect-video">
            <img src="{{ $portfolio->cover_url }}" alt="{{ $portfolio->title }}" class="w-full h-full object-cover">
        </div>
    @else
        <div
            class="mb-3 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 aspect-video flex items-center justify-center">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    @endif

    {{-- Title & Status --}}
    <div class="flex items-start justify-between gap-2 mb-2">
        <h3 class="font-semibold text-slate-900 line-clamp-1">{{ $portfolio->title }}</h3>
        <div class="flex items-center gap-1 shrink-0">
            @if($isBuiltFromCourse)
                <span
                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                    Built from Course
                </span>
            @endif
            <span
                class="px-2 py-0.5 rounded-full text-xs font-medium portfolio-status-badge
                {{ $isPublished ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                {{ $isPublished ? 'Published' : 'Draft' }}
            </span>
        </div>
    </div>

    {{-- Source Course Info --}}
    @if($isBuiltFromCourse && $sourceCourse)
        <div class="mb-2 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ $sourceCourse->course_name ?: $sourceCourse->course_id }}
            </span>
        </div>
    @endif

    {{-- Description --}}
    @if($portfolio->description)
        <p class="text-sm text-slate-600 line-clamp-2 mb-3">{{ $portfolio->description }}</p>
    @endif

    {{-- Evidence Section --}}
    @if($evidences->count() > 0)
        <div class="mb-3 pt-3 border-t border-slate-100">
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
    <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
        <span class="text-xs text-slate-400">
            {{ $portfolio->created_at->diffForHumans() }}
        </span>
        <div class="flex items-center gap-2">
            {{-- Edit Button --}}
            <button type="button" data-edit-portfolio data-portfolio-json='@json($wizardData)'
                class="p-1.5 text-slate-400 hover:text-blue-600 transition-all duration-150 hover:scale-110"
                title="Edit Portfolio">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>

            {{-- Add Evidence Button --}}
            <button type="button" data-open-evidence-modal data-item-type="portfolio"
                data-item-id="{{ $portfolio->id }}"
                class="p-1.5 text-slate-400 hover:text-blue-600 transition-all duration-150 hover:scale-110"
                title="Tambah Evidence">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
            </button>

            {{-- Delete Button --}}
            <form method="POST" action="{{ route('dashboard.portfolio.destroy', $portfolio) }}" data-confirm-delete>
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="p-1.5 text-slate-400 hover:text-red-600 transition-all duration-150 hover:scale-110">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>

            {{-- Publish/Unpublish Button --}}
            @if($isPublished)
                <form method="POST" action="{{ route('dashboard.portfolio.unpublish', $portfolio) }}"
                    class="portfolio-publish-form">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
                        Unpublish
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('dashboard.portfolio.publish', $portfolio) }}"
                    class="portfolio-publish-form">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98] shadow-sm">
                        Publish
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>