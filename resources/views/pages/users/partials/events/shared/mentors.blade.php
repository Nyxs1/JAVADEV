{{-- Admin Event Mentors Management --}}
<div class="space-y-6">
    {{-- Add Mentor Form --}}
    @if(isset($availableMentors) && $availableMentors->count() > 0)
        <div class="bg-slate-50 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Assign Mentor</h3>
            <form action="{{ route('users.mentors.store', $adminEvent->slug) }}" method="POST" class="flex flex-wrap gap-3">
                @csrf
                <select name="user_id" required class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select mentor...</option>
                    @foreach($availableMentors as $mentor)
                        <option value="{{ $mentor->id }}">{{ $mentor->name ?? $mentor->username }} ({{ $mentor->email }})</option>
                    @endforeach
                </select>
                <select name="role" required class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="mentor">Mentor</option>
                    <option value="co-mentor">Co-Mentor</option>
                    <option value="speaker">Speaker</option>
                    <option value="moderator">Moderator</option>
                </select>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Assign
                </button>
            </form>
        </div>
    @endif

    {{-- Mentors List --}}
    @if(isset($eventMentors) && $eventMentors->count() > 0)
        <div class="space-y-3">
            @foreach($eventMentors as $mentor)
                <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        @if($mentor->user?->avatar)
                            <img src="{{ asset('storage/' . $mentor->user->avatar) }}" alt=""
                                class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-sm font-medium text-slate-600">
                                {{ strtoupper(substr($mentor->user?->username ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-slate-900">{{ $mentor->user?->name ?? $mentor->user?->username ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">{{ $mentor->user?->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-700">
                            {{ ucfirst($mentor->role) }}
                        </span>
                        <form action="{{ route('users.mentors.destroy', [$adminEvent->slug, $mentor->id]) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <img src="{{ asset('assets/icons/trash.svg') }}" alt="" class="w-4 h-4">
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <img src="{{ asset('assets/icons/users.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
            <p class="text-slate-500">Belum ada mentor yang ditugaskan</p>
        </div>
    @endif
</div>
