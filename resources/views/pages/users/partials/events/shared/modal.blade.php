{{-- Create Event Modal --}}
<div id="create-event-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" data-modal-backdrop></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Create Event</h2>
                <button type="button" data-modal-close
                    class="p-2 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
                    <img src="{{ asset('assets/icons/x.svg') }}" alt="" class="w-5 h-5">
                </button>
            </div>

            <form action="{{ route('users.events.store') }}" method="POST" enctype="multipart/form-data"
                class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                        <input type="text" name="title" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="3" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                        <select name="type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="workshop">Workshop</option>
                            <option value="seminar">Seminar</option>
                            <option value="mentoring">Mentoring</option>
                        </select>
                    </div>

                    {{-- Level --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Level</label>
                        <select name="level" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1">Beginner</option>
                            <option value="2">Fundamental</option>
                            <option value="3">Intermediate</option>
                            <option value="4">Advanced</option>
                        </select>
                    </div>

                    {{-- Mode --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mode</label>
                        <select name="mode" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="online">Online</option>
                            <option value="onsite">Onsite</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>

                    {{-- Capacity --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Capacity (optional)</label>
                        <input type="number" name="capacity" min="1"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Start At --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Start Date/Time</label>
                        <input type="datetime-local" name="start_at" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- End At --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">End Date/Time</label>
                        <input type="datetime-local" name="end_at" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Location (optional)</label>
                        <input type="text" name="location_text"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Meeting URL --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Meeting URL (optional)</label>
                        <input type="url" name="meeting_url"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Cover Image --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Cover Image</label>
                        <input type="file" name="cover_image" accept="image/*"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <button type="button" data-modal-close
                        class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>