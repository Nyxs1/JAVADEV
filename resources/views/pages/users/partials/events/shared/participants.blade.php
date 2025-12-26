{{-- Mentor Participants List --}}
@if($participants->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Participant</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Joined</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Attendance</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Completion</th>
                    @if($mentorEvent->isOngoing())
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($participants as $participant)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($participant->user?->avatar)
                                    <img src="{{ asset('storage/' . $participant->user->avatar) }}" alt=""
                                        class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-sm font-medium text-slate-600">
                                        {{ strtoupper(substr($participant->user?->username ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-slate-900">{{ $participant->user?->name ?? $participant->user?->username ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500">{{ $participant->user?->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $participant->joined_at ? \Carbon\Carbon::parse($participant->joined_at)->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($participant->attendance_status === 'present')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Present</span>
                            @elseif($participant->attendance_status === 'absent')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Absent</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($participant->completion_status === 'completed')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Completed</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Pending</span>
                            @endif
                        </td>
                        @if($mentorEvent->isOngoing())
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($participant->attendance_status !== 'present')
                                        <form action="{{ route('users.participants.present', [$mentorEvent->slug, $participant->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-green-600 hover:bg-green-50 rounded transition-colors">
                                                Mark Present
                                            </button>
                                        </form>
                                    @endif
                                    @if($participant->attendance_status === 'present' && $participant->completion_status !== 'completed')
                                        <form action="{{ route('users.participants.completed', [$mentorEvent->slug, $participant->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                                Mark Completed
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-8">
        <img src="{{ asset('assets/icons/users.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
        <p class="text-slate-500">Belum ada peserta terdaftar</p>
    </div>
@endif
