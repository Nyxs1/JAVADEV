@props(['user'])

<div class="bg-white rounded-lg border border-slate-200 p-6" x-data="roleRequestModal()">
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
                        <button type="button" @click="openModal('mentor')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
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
                        <button type="button" @click="openModal('member')"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
                            Request
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Role Request Modal --}}
    <div x-show="isOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="closeModal()">
        <div class="w-full h-full flex items-center justify-center">
            <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl" x-show="isOpen"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                @click.stop>
                <h3 class="text-lg font-semibold text-slate-900 mb-4" x-text="modalTitle">Request Role Change</h3>

                <form method="POST" action="{{ route('profile.role-request') }}">
                    @csrf
                    <input type="hidden" name="to_role_id" :value="toRoleId">

                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-slate-700 mb-2">
                            Reason for request (optional)
                        </label>
                        <textarea name="reason" id="reason" rows="3" placeholder="Why do you want to change your role?"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                        <p class="text-xs text-slate-500 mt-1">Maximum 500 characters</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-all duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-150 hover:scale-[1.02] active:scale-[0.98]">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('roleRequestModal', () => ({
            isOpen: false,
            toRoleId: null,
            modalTitle: 'Request Role Change',

            // Role IDs (these should match your database)
            roleIds: {
                'mentor': 2,  // Adjust based on your roles table
                'member': 1,  // Adjust based on your roles table
            },

            openModal(role) {
                this.toRoleId = this.roleIds[role] || null;
                this.modalTitle = role === 'mentor'
                    ? 'Request to Become Mentor'
                    : 'Request to Step Down';
                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.isOpen = false;
                document.body.style.overflow = '';
            }
        }));
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>