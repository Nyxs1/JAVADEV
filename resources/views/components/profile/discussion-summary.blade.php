@props(['summary', 'user', 'isOwnProfile' => false])

@php
    $totalThreads = $summary['totalThreads'] ?? 0;
    $totalReplies = $summary['totalReplies'] ?? 0;
    $recentParticipation = $summary['recentParticipation'] ?? [];
    $hasActivity = $totalThreads > 0 || $totalReplies > 0;
@endphp

<div class="discussion-summary">
    @if($hasActivity)
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="p-4 bg-slate-50 rounded-lg text-center">
                <div class="text-2xl font-bold text-slate-900">{{ $totalThreads }}</div>
                <div class="text-sm text-slate-600">Threads Started</div>
            </div>
            <div class="p-4 bg-slate-50 rounded-lg text-center">
                <div class="text-2xl font-bold text-slate-900">{{ $totalReplies }}</div>
                <div class="text-sm text-slate-600">Replies Posted</div>
            </div>
        </div>

        @if(count($recentParticipation) > 0)
            <div class="border-t border-slate-200 pt-4">
                <h4 class="text-sm font-medium text-slate-700 mb-3">Recent Participation</h4>
                <div class="space-y-3">
                    @foreach($recentParticipation as $item)
                        <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                            <div class="shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <img src="{{ asset('assets/icons/message-circle.svg') }}" alt="" class="w-4 h-4 opacity-60">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-900 line-clamp-1">{{ $item['title'] }}</p>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span>{{ $item['type'] === 'thread' ? 'Started thread' : 'Replied' }}</span>
                                    @if(isset($item['date']))
                                        <span>{{ $item['date'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <img src="{{ asset('assets/icons/message-circle.svg') }}" alt="" class="w-8 h-8 opacity-50">
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-2">No Discussions Yet</h3>
            <p class="text-slate-600 max-w-sm mx-auto">
                @if($isOwnProfile)
                    You have not participated in any discussions. Join the community and start engaging!
                @else
                    This user has not participated in any discussions yet.
                @endif
            </p>
        </div>
    @endif
</div>