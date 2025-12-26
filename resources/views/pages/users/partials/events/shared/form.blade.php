{{-- Admin Event Edit Form --}}
<form action="{{ route('users.events.update', $adminEvent->slug) }}" method="POST" enctype="multipart/form-data"
    class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Title --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $adminEvent->title) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('title')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="4" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $adminEvent->description) }}</textarea>
            @error('description')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
            <select name="type" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="workshop" {{ old('type', $adminEvent->type) === 'workshop' ? 'selected' : '' }}>Workshop
                </option>
                <option value="seminar" {{ old('type', $adminEvent->type) === 'seminar' ? 'selected' : '' }}>Seminar
                </option>
                <option value="mentoring" {{ old('type', $adminEvent->type) === 'mentoring' ? 'selected' : '' }}>Mentoring
                </option>
            </select>
        </div>

        {{-- Level --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Level</label>
            <select name="level" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="1" {{ old('level', $adminEvent->level) == 1 ? 'selected' : '' }}>Beginner</option>
                <option value="2" {{ old('level', $adminEvent->level) == 2 ? 'selected' : '' }}>Fundamental</option>
                <option value="3" {{ old('level', $adminEvent->level) == 3 ? 'selected' : '' }}>Intermediate</option>
                <option value="4" {{ old('level', $adminEvent->level) == 4 ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>

        {{-- Mode --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mode</label>
            <select name="mode" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="online" {{ old('mode', $adminEvent->mode) === 'online' ? 'selected' : '' }}>Online</option>
                <option value="onsite" {{ old('mode', $adminEvent->mode) === 'onsite' ? 'selected' : '' }}>Onsite</option>
                <option value="hybrid" {{ old('mode', $adminEvent->mode) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
            </select>
        </div>

        {{-- Capacity --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Capacity (optional)</label>
            <input type="number" name="capacity" value="{{ old('capacity', $adminEvent->capacity) }}" min="1"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Start At --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Start Date/Time</label>
            <input type="datetime-local" name="start_at"
                value="{{ old('start_at', $adminEvent->start_at->format('Y-m-d\TH:i')) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- End At --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">End Date/Time</label>
            <input type="datetime-local" name="end_at"
                value="{{ old('end_at', $adminEvent->end_at->format('Y-m-d\TH:i')) }}" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Location --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Location (optional)</label>
            <input type="text" name="location_text" value="{{ old('location_text', $adminEvent->location_text) }}"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Meeting URL --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Meeting URL (optional)</label>
            <input type="url" name="meeting_url" value="{{ old('meeting_url', $adminEvent->meeting_url) }}"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Cover Image --}}
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Cover Image</label>
            @if($adminEvent->cover_image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $adminEvent->cover_image) }}" alt=""
                        class="w-32 h-24 rounded-lg object-cover">
                </div>
            @endif
            <input type="file" name="cover_image" accept="image/*"
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
            <select name="status" required
                class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="draft" {{ old('status', $adminEvent->status) === 'draft' ? 'selected' : '' }}>Draft
                </option>
                <option value="published" {{ old('status', $adminEvent->status) === 'published' ? 'selected' : '' }}>
                    Published</option>
                <option value="cancelled" {{ old('status', $adminEvent->status) === 'cancelled' ? 'selected' : '' }}>
                    Cancelled</option>
                <option value="ended" {{ old('status', $adminEvent->status) === 'ended' ? 'selected' : '' }}>Ended
                </option>
            </select>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between pt-6 border-t border-slate-200">
        <form action="{{ route('users.events.destroy', $adminEvent->slug) }}" method="POST" class="inline"
            onsubmit="return confirm('Are you sure you want to delete this event?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                Delete Event
            </button>
        </form>
        <button type="submit"
            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
            Save Changes
        </button>
    </div>
</form>