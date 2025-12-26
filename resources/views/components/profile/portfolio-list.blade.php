@props(['activities', 'user', 'isOwnProfile' => false])

<div class="portfolio-list">
    @if(count($activities) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($activities as $item)
                <div
                    class="portfolio-item p-4 border border-slate-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all">
                    @if(isset($item['image']) && $item['image'])
                        <div class="mb-3 rounded-lg overflow-hidden bg-slate-100">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-32 object-cover"
                                onerror="this.style.display='none'">
                        </div>
                    @endif

                    <h4 class="font-medium text-slate-900">{{ $item['title'] }}</h4>

                    @if(isset($item['description']))
                        <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                    @endif

                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-500">
                        @if(isset($item['date']))
                            <span class="flex items-center gap-1">
                                <img src="{{ asset('assets/icons/calendar.svg') }}" alt="" class="w-3 h-3 opacity-60">
                                {{ $item['date'] }}
                            </span>
                        @endif
                    </div>

                    @if(isset($item['tags']) && count($item['tags']) > 0)
                        <div class="flex flex-wrap gap-1 mt-3">
                            @foreach(array_slice($item['tags'], 0, 3) as $tag)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">
                                    {{ $tag }}
                                </span>
                            @endforeach
                            @if(count($item['tags']) > 3)
                                <span class="px-2 py-0.5 text-xs text-slate-400">+{{ count($item['tags']) - 3 }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <img src="{{ asset('assets/icons/briefcase.svg') }}" alt="" class="w-8 h-8 opacity-50">
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-2">No Portfolio Yet</h3>
            <p class="text-slate-600 max-w-sm mx-auto">
                @if($isOwnProfile)
                    You have not created any portfolio projects. Start building your portfolio to showcase your skills!
                @else
                    This user has not created any portfolio projects yet.
                @endif
            </p>
        </div>
    @endif
</div>