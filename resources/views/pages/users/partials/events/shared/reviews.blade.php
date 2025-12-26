{{-- Admin Event Reviews Moderation --}}
<div class="space-y-6">
    {{-- Rating Summary --}}
    @if(isset($avgRating) && $avgRating)
        <div class="bg-slate-50 rounded-lg p-4">
            <div class="flex items-center gap-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($avgRating, 1) }}</p>
                    <div class="flex items-center justify-center gap-0.5 mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <img src="{{ asset('assets/icons/star' . ($i <= round($avgRating) ? '-filled' : '') . '.svg') }}"
                                alt="" class="w-4 h-4">
                        @endfor
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $reviewCount ?? 0 }} reviews</p>
                </div>

                {{-- Rating Distribution --}}
                @if(isset($ratingDistribution))
                    <div class="flex-1 space-y-1">
                        @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500 w-3">{{ $i }}</span>
                                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                    @php
                                        $percentage = ($reviewCount ?? 0) > 0 ? (($ratingDistribution[$i] ?? 0) / $reviewCount) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-amber-400 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="text-xs text-slate-500 w-6">{{ $ratingDistribution[$i] ?? 0 }}</span>
                            </div>
                        @endfor
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Reviews List --}}
    @if(isset($eventReviews) && $eventReviews->count() > 0)
        <div class="space-y-4">
            @foreach($eventReviews as $review)
                <div class="p-4 bg-white border border-slate-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        @if($review->fromUser?->avatar)
                            <img src="{{ asset('storage/' . $review->fromUser->avatar) }}" alt=""
                                class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-sm font-medium text-slate-600">
                                {{ strtoupper(substr($review->fromUser?->username ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $review->fromUser?->name ?? $review->fromUser?->username ?? 'Anonymous' }}</p>
                                    <div class="flex items-center gap-0.5 mt-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <img src="{{ asset('assets/icons/star' . ($i <= $review->rating ? '-filled' : '') . '.svg') }}"
                                                alt="" class="w-4 h-4">
                                        @endfor
                                    </div>
                                </div>
                                <form action="{{ route('users.reviews.destroy', [$adminEvent->slug, $review->id]) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="" class="w-4 h-4">
                                    </button>
                                </form>
                            </div>
                            @if($review->comment)
                                <p class="text-sm text-slate-600 mt-2">{{ $review->comment }}</p>
                            @endif
                            <p class="text-xs text-slate-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($eventReviews->hasPages())
            <div class="pt-4">
                {{ $eventReviews->appends(['tab' => 'admin', 'event' => $adminEvent->slug, 'subtab' => 'reviews'])->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <img src="{{ asset('assets/icons/star.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
            <p class="text-slate-500">Belum ada review</p>
        </div>
    @endif
</div>
