@extends('layouts.app')

@section('title', 'Edit Requirements')

@section('content')
    <div class="requirements-edit">
        <div class="requirements-edit__header">
            <a href="{{ route('events.show', $event) }}" class="requirements-edit__back">
                <img src="{{ asset('assets/icons/arrow-left.svg') }}" alt="" class="w-5 h-5">
                Back to Event
            </a>
            <h1 class="requirements-edit__title">Edit Requirements</h1>
            <p class="requirements-edit__subtitle">{{ $event->title }}</p>
        </div>

        @if($isLocked)
            <div class="requirements-edit__locked">
                <img src="{{ asset('assets/icons/lock.svg') }}" alt="" class="requirements-edit__locked-icon">
                <p>Requirements are locked because the event has already started.</p>
            </div>
        @else
            <form action="{{ route('events.requirements.update', $event) }}" method="POST" class="requirements-edit__form"
                data-requirements-form>
                @csrf
                @method('PUT')

                {{-- Skills --}}
                <div class="requirements-edit__section">
                    <h2 class="requirements-edit__section-title">Skills</h2>
                    <p class="requirements-edit__section-desc">Required skills participants should have.</p>
                    <div class="requirements-edit__repeater" data-repeater="skills">
                        @forelse($event->requirements['skills'] ?? [] as $skill)
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="skills[]" value="{{ $skill }}" placeholder="e.g., Basic JavaScript">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @empty
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="skills[]" placeholder="e.g., Basic JavaScript">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="requirements-edit__add" data-add-item="skills">+ Add Skill</button>
                </div>

                {{-- Tech Stack --}}
                <div class="requirements-edit__section">
                    <h2 class="requirements-edit__section-title">Tech Stack</h2>

                    {{-- Languages --}}
                    <div class="requirements-edit__subsection">
                        <h3>Languages</h3>
                        <div class="requirements-edit__repeater" data-repeater="tech_stack.language">
                            @forelse($event->requirements['tech_stack']['language'] ?? [] as $item)
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[language][]" value="{{ $item }}"
                                        placeholder="e.g., JavaScript">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @empty
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[language][]" placeholder="e.g., JavaScript">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="requirements-edit__add" data-add-item="tech_stack.language">+ Add
                            Language</button>
                    </div>

                    {{-- Frameworks --}}
                    <div class="requirements-edit__subsection">
                        <h3>Frameworks</h3>
                        <div class="requirements-edit__repeater" data-repeater="tech_stack.framework">
                            @forelse($event->requirements['tech_stack']['framework'] ?? [] as $item)
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[framework][]" value="{{ $item }}"
                                        placeholder="e.g., React 18">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @empty
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[framework][]" placeholder="e.g., React 18">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="requirements-edit__add" data-add-item="tech_stack.framework">+ Add
                            Framework</button>
                    </div>

                    {{-- Database --}}
                    <div class="requirements-edit__subsection">
                        <h3>Database</h3>
                        <div class="requirements-edit__repeater" data-repeater="tech_stack.database">
                            @forelse($event->requirements['tech_stack']['database'] ?? [] as $item)
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[database][]" value="{{ $item }}"
                                        placeholder="e.g., MongoDB">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @empty
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[database][]" placeholder="e.g., MongoDB">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="requirements-edit__add" data-add-item="tech_stack.database">+ Add
                            Database</button>
                    </div>

                    {{-- Tools --}}
                    <div class="requirements-edit__subsection">
                        <h3>Tools</h3>
                        <div class="requirements-edit__repeater" data-repeater="tech_stack.tools">
                            @forelse($event->requirements['tech_stack']['tools'] ?? [] as $item)
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[tools][]" value="{{ $item }}" placeholder="e.g., VS Code">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @empty
                                <div class="requirements-edit__repeater-item">
                                    <input type="text" name="tech_stack[tools][]" placeholder="e.g., VS Code">
                                    <button type="button" class="requirements-edit__remove" data-remove-item>
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="requirements-edit__add" data-add-item="tech_stack.tools">+ Add
                            Tool</button>
                    </div>
                </div>

                {{-- Accounts --}}
                <div class="requirements-edit__section">
                    <h2 class="requirements-edit__section-title">Accounts & Access</h2>
                    <p class="requirements-edit__section-desc">Required accounts participants need to have.</p>
                    <div class="requirements-edit__repeater" data-repeater="accounts">
                        @forelse($event->requirements['accounts'] ?? [] as $account)
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="accounts[]" value="{{ $account }}" placeholder="e.g., GitHub">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @empty
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="accounts[]" placeholder="e.g., GitHub">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="requirements-edit__add" data-add-item="accounts">+ Add Account</button>
                </div>

                {{-- Checklist --}}
                <div class="requirements-edit__section">
                    <h2 class="requirements-edit__section-title">Preparation Checklist</h2>
                    <p class="requirements-edit__section-desc">Steps participants should complete before the event.</p>
                    <div class="requirements-edit__repeater" data-repeater="checklist">
                        @forelse($event->requirements['checklist'] ?? [] as $item)
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="checklist[]" value="{{ $item }}" placeholder="e.g., Install Node.js">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @empty
                            <div class="requirements-edit__repeater-item">
                                <input type="text" name="checklist[]" placeholder="e.g., Install Node.js">
                                <button type="button" class="requirements-edit__remove" data-remove-item>
                                    <img src="{{ asset('assets/icons/trash.svg') }}" alt="Remove">
                                </button>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="requirements-edit__add" data-add-item="checklist">+ Add Item</button>
                </div>

                {{-- Notes --}}
                <div class="requirements-edit__section">
                    <h2 class="requirements-edit__section-title">Notes</h2>
                    <p class="requirements-edit__section-desc">Additional information for participants.</p>
                    <textarea name="notes" rows="4"
                        placeholder="Any additional notes...">{{ $event->requirements['notes'] ?? '' }}</textarea>
                </div>

                <div class="requirements-edit__actions">
                    <a href="{{ route('events.show', $event) }}" class="btn btn--secondary">Cancel</a>
                    <button type="submit" class="btn btn--primary">Save Requirements</button>
                </div>
            </form>
        @endif
    </div>
@endsection