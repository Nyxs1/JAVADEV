@props(['activities', 'user', 'isOwnProfile' => false])

<div class="courses-list">
    @if(count($activities) > 0)
        <div class="space-y-4">
            @foreach($activities as $item)
                <div
                    class="course-item p-4 border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all">
                    <div class="flex items-start gap-4">
                        @if(isset($item['image']) && $item['image'])
                            <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-slate-100">
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover"
                                    onerror="this.style.display='none'">
                            </div>
                        @else
                            <div class="shrink-0 w-16 h-16 rounded-lg bg-blue-100 flex items-center justify-center">
                                <img src="{{ asset('assets/icons/book.svg') }}" alt="" class="w-8 h-8 opacity-60">
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-medium text-slate-900">{{ $item['title'] }}</h4>
                                @if(isset($item['type']))
                                    <span
                                        class="shrink-0 px-2 py-0.5 rounded-full text-xs font-medium {{ $item['type'] === 'challenge' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ ucfirst($item['type']) }}
                                    </span>
                                @endif
                            </div>

                            @if(isset($item['description']))
                                <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                            @endif

                            <div class="flex items-center gap-4 mt-3">
                                @if(isset($item['progress']))
                                    <div class="flex items-center gap-2 flex-1 max-w-xs">
                                        <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full transition-all"
                                                style="width: {{ $item['progress'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 shrink-0">{{ $item['progress'] }}%</span>
                                    </div>
                                @endif

                                @if(isset($item['status']))
                                    <span class="flex items-center gap-1 text-xs">
                                        <div
                                            class="w-2 h-2 rounded-full {{ $item['status'] === 'completed' ? 'bg-green-500' : ($item['status'] === 'in-progress' ? 'bg-yellow-500' : 'bg-slate-400') }}">
                                        </div>
                                        <span class="text-slate-500">{{ ucfirst(str_replace('-', ' ', $item['status'])) }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <img src="{{ asset('assets/icons/book.svg') }}" alt="" class="w-8 h-8 opacity-50">
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-2">No Courses Yet</h3>
            <p class="text-slate-600 max-w-sm mx-auto">
                @if($isOwnProfile)
                    You have not enrolled in any courses. Check out the course catalog and start learning!
                @else
                    This user has not enrolled in any courses yet.
                @endif
            </p>
        </div>
    @endif
</div>