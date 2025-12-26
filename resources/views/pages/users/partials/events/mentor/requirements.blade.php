{{-- Mentor Requirements Management --}}
<div class="space-y-6">
    {{-- Add Requirement Form --}}
    @if(!$requirementsLocked)
        <div class="bg-slate-50 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Add Requirement</h3>
            <form action="{{ route('users.requirements.store', $mentorEvent->slug) }}" method="POST"
                class="flex flex-wrap gap-3">
                @csrf
                <input type="text" name="title" placeholder="Requirement title" required
                    class="flex-1 min-w-[200px] px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <select name="type" required
                    class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="info">Info</option>
                    <option value="checklist">Checklist</option>
                    <option value="tech">Tech Stack</option>
                </select>
                <select name="category"
                    class="px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Category (for tech)</option>
                    <option value="tools">Tools</option>
                    <option value="language">Language</option>
                    <option value="framework">Framework</option>
                    <option value="database">Database</option>
                    <option value="other">Other</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Add
                </button>
            </form>
        </div>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <p class="text-sm text-amber-700">Requirements are locked after event starts.</p>
        </div>
    @endif

    {{-- Info Requirements --}}
    @if($infoRequirements->count() > 0)
        <div>
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Information</h3>
            <div class="space-y-2">
                @foreach($infoRequirements as $req)
                    <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $req->title }}</span>
                        @if(!$requirementsLocked)
                            <form action="{{ route('users.requirements.destroy', [$mentorEvent->slug, $req->id]) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition-colors">
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="" class="w-4 h-4">
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Checklist Requirements --}}
    @if($checklistRequirements->count() > 0)
        <div>
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Checklist</h3>
            <div class="space-y-2">
                @foreach($checklistRequirements as $req)
                    <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $req->title }}</span>
                        @if(!$requirementsLocked)
                            <form action="{{ route('users.requirements.destroy', [$mentorEvent->slug, $req->id]) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 transition-colors">
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="" class="w-4 h-4">
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tech Requirements --}}
    @if($techRequirements->count() > 0)
        <div>
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Tech Stack</h3>
            @foreach($techRequirements as $category => $items)
                <div class="mb-4">
                    <p class="text-xs font-medium text-slate-500 uppercase mb-2">{{ ucfirst($category) }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($items as $req)
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-full">
                                <span class="text-sm text-slate-700">{{ $req->title }}</span>
                                @if(!$requirementsLocked)
                                    <form action="{{ route('users.requirements.destroy', [$mentorEvent->slug, $req->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors">
                                            <img src="{{ asset('assets/icons/x.svg') }}" alt="" class="w-3 h-3">
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Empty State --}}
    @if($infoRequirements->count() === 0 && $checklistRequirements->count() === 0 && $techRequirements->count() === 0)
        <div class="text-center py-8">
            <img src="{{ asset('assets/icons/clipboard.svg') }}" alt="" class="w-12 h-12 mx-auto opacity-40 mb-3">
            <p class="text-slate-500">Belum ada requirements</p>
        </div>
    @endif
</div>