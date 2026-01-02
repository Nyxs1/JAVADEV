@props(['activities', 'user', 'isOwnProfile' => false])

<div class="portfolio-list">
    @if(count($activities) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($activities as $item)
                @php
                    // Support both Eloquent models and array format
                    $isModel = $item instanceof \App\Models\Portfolio;
                    $title = $isModel ? $item->title : ($item['title'] ?? '');
                    $description = $isModel ? $item->description : ($item['description'] ?? null);
                    $readmeMd = $isModel ? $item->readme_md : null;
                    $image = $isModel ? $item->cover_url : ($item['image'] ?? null);
                    $date = $isModel ? $item->created_at->format('M Y') : ($item['date'] ?? null);
                    $isPublished = $isModel ? $item->is_published : true;
                    $isBuiltFromCourse = $isModel && method_exists($item, 'isBuiltFromCourse') ? $item->isBuiltFromCourse() : false;
                    $evidences = $isModel ? ($item->evidences ?? collect()) : collect();
                    $screenshots = $isModel ? ($item->screenshots ?? collect()) : collect();
                    $publicEvidences = $evidences->where('is_public', true)->take(3);
                @endphp
                <div
                    class="portfolio-item p-4 border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all bg-white">
                    {{-- Show draft badge for own profile --}}
                    @if($isOwnProfile && !$isPublished)
                        <div class="mb-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                Draft
                            </span>
                        </div>
                    @endif

                    {{-- Built from Course badge --}}
                    @if($isBuiltFromCourse)
                        <div class="mb-2">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                Built from Course
                            </span>
                        </div>
                    @endif

                    @if($image)
                        <div class="mb-3 rounded-lg overflow-hidden bg-slate-100 aspect-video">
                            <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover"
                                onerror="this.parentElement.style.display='none'">
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

                    <h4 class="font-medium text-slate-900">{{ $title }}</h4>

                    @if($description)
                        <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $description }}</p>
                    @endif

                    {{-- README Markdown (collapsible for long content) --}}
                    @if($readmeMd)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <details class="group">
                                <summary
                                    class="flex items-center gap-1 text-xs font-medium text-slate-500 cursor-pointer hover:text-slate-700">
                                    <svg class="w-3 h-3 transition-transform group-open:rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    README
                                </summary>
                                <div class="mt-2 prose prose-sm prose-slate max-w-none">
                                    {!! Str::markdown($readmeMd) !!}
                                </div>
                            </details>
                        </div>
                    @endif

                    {{-- Screenshot Gallery --}}
                    @if($screenshots->count() > 0)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <p class="text-xs font-medium text-slate-500 mb-2">Screenshots</p>
                            <div class="grid grid-cols-3 gap-1">
                                @foreach($screenshots->take(6) as $screenshot)
                                    <a href="{{ $screenshot->url }}" target="_blank" rel="noopener"
                                        class="aspect-video rounded overflow-hidden bg-slate-100 hover:opacity-90 transition-opacity">
                                        <img src="{{ $screenshot->url }}" alt="{{ $screenshot->caption ?? 'Screenshot' }}"
                                            class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                            @if($screenshots->count() > 6)
                                <p class="text-xs text-slate-400 mt-1">+{{ $screenshots->count() - 6 }} more</p>
                            @endif
                        </div>
                    @endif

                    {{-- Evidence Links --}}
                    @if($publicEvidences->count() > 0)
                        <div class="flex items-center gap-2 mt-3 flex-wrap">
                            @foreach($publicEvidences as $evidence)
                                <a href="{{ $evidence->value }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-slate-100 hover:bg-slate-200 rounded-md transition-colors"
                                    title="{{ $evidence->display_label }}">
                                    @if($evidence->type === 'github')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" />
                                        </svg>
                                    @elseif($evidence->type === 'demo')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    @endif
                                    <span>{{ Str::limit($evidence->display_label, 12) }}</span>
                                </a>
                            @endforeach
                            @if($evidences->where('is_public', true)->count() > 3)
                                <span class="text-xs text-slate-400">+{{ $evidences->where('is_public', true)->count() - 3 }}</span>
                            @endif
                        </div>
                    @endif

                    @if($date)
                        <div class="flex items-center gap-3 mt-3 text-xs text-slate-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $date }}
                            </span>
                        </div>
                    @endif
                </div>
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
                @if($isOwnProfile)
                    Kamu belum membuat portfolio apapun.
                    <a href="{{ route('dashboard.portfolio.index') }}" class="text-blue-600 hover:underline">
                        Mulai tambahkan project →
                    </a>
                @else
                    Belum ada portfolio yang dibagikan.
                @endif
            </p>
        </div>
    @endif
</div>