@props(['activities', 'user', 'isOwnProfile' => false])

<div class="courses-list">
    @if(count($activities) > 0)
        <div class="space-y-4">
            @foreach($activities as $item)
                @php
                    // Support both Eloquent models and array format
                    $isModel = $item instanceof \App\Models\UserCourse;
                    $title = $isModel ? ($item->course_name ?: $item->course_id) : ($item['title'] ?? '');
                    $description = $isModel ? null : ($item['description'] ?? null);
                    $progress = $isModel ? $item->progress_percent : ($item['progress'] ?? 0);
                    $status = $isModel ? $item->status : ($item['status'] ?? null);
                    $isCompleted = $isModel ? $item->isCompleted() : ($status === 'completed');
                    $isPublished = $isModel ? $item->is_published : true;
                    $statusLabel = $isModel ? $item->status_label : ucfirst(str_replace('-', ' ', $status ?? ''));
                    $evidences = $isModel ? ($item->evidences ?? collect()) : collect();
                    $publicEvidences = $evidences->where('is_public', true)->take(3);
                @endphp
                <div
                    class="course-item p-4 border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all bg-white">
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

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-medium text-slate-900">{{ $title }}</h4>
                                <div class="flex items-center gap-2">
                                    @if($status)
                                        <span
                                            class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium 
                                                                    {{ $isCompleted ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $statusLabel }}
                                        </span>
                                    @endif
                                    @if($isOwnProfile && !$isPublished)
                                        <span
                                            class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                            Draft
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($description)
                                <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $description }}</p>
                            @endif

                            {{-- Progress Bar --}}
                            <div class="flex items-center gap-3 mt-3">
                                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all 
                                                        {{ $isCompleted ? 'bg-green-500' : 'bg-blue-500' }}"
                                        style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-600 shrink-0">{{ $progress }}%</span>
                            </div>

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
                                        <span
                                            class="text-xs text-slate-400">+{{ $evidences->where('is_public', true)->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
                @if($isOwnProfile)
                    Kamu belum mengikuti course apapun.
                    <a href="{{ route('dashboard.courses.index') }}" class="text-blue-600 hover:underline">
                        Lihat course yang tersedia →
                    </a>
                @else
                    Belum ada course yang ditampilkan secara publik.
                @endif
            </p>
        </div>
    @endif
</div>