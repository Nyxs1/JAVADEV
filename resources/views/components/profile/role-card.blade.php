@props(['user'])

<div class="bg-white rounded-lg border border-slate-200 p-6">
    <h3 class="text-lg font-semibold text-slate-900 mb-4">Role Management</h3>

    {{-- Current Role --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">Current Role</label>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ $user->role ? ucfirst($user->role->name) : 'Member' }}
            </span>
        </div>
    </div>

    {{-- Pending Request Status --}}
    @if($user->hasPendingRoleRequest())
        @php $pendingRequest = $user->pendingRoleRequest(); @endphp
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
                <img src="{{ asset('assets/icons/warning.svg') }}" alt="" class="w-4 h-4 shrink-0">
                <span class="text-sm font-medium text-yellow-800">Pending Role Request</span>
            </div>
            <p class="text-sm text-yellow-700">
                You have requested to become a <strong>{{ ucfirst($pendingRequest->toRole->name) }}</strong>.
                <br>Status: <span class="font-medium">{{ ucfirst($pendingRequest->status) }}</span>
                <br>Submitted: {{ $pendingRequest->created_at->format('M d, Y') }}
            </p>
        </div>
    @else
        {{-- Role Change Options --}}
        <div class="space-y-3">
            @if($user->isMember())
                <div class="border border-slate-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-slate-900">Become a Mentor</h4>
                            <p class="text-sm text-slate-600">Share your knowledge and guide other members</p>
                        </div>
                        <button type="button" onclick="openRoleRequestModal('mentor')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Request
                        </button>
                    </div>
                </div>
            @elseif($user->isMentor())
                <div class="border border-slate-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-slate-900">Step Down to Member</h4>
                            <p class="text-sm text-slate-600">Return to regular member status</p>
                        </div>
                        <button type="button" onclick="openRoleRequestModal('member')"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            Request
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Role Request Modal --}}
<div id="role-request-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-4">
    {{-- ⬇️ wrapper ini yang pegang flex, jadi nggak tabrakan sama "hidden" --}}
    <div class="w-full h-full flex items-center justify-center">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-4" id="modal-title">Request Role Change</h3>

            <form method="POST" action="{{ route('profile.role-request') }}" id="role-request-form">
                @csrf
                <input type="hidden" name="to_role_id" id="to_role_id">

                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-slate-700 mb-2">
                        Reason for request (optional)
                    </label>
                    <textarea name="reason" id="reason" rows="3" placeholder="Why do you want to change your role?"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    <p class="text-xs text-slate-500 mt-1">Maximum 500 characters</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRoleRequestModal()"
                        class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>