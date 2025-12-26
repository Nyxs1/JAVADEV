<div class="event-requirements">
    <h2 class="event-section__title">Requirements</h2>

    @php
        $hasAnyRequirements = $infoRequirements->isNotEmpty() 
            || $checklistRequirements->isNotEmpty() 
            || $techRequirements->isNotEmpty();
    @endphp

    @if(!$hasAnyRequirements)
        <div class="event-empty">
            <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-empty__icon">
            <p class="event-empty__text">Requirements belum tersedia. Cek lagi nanti.</p>
        </div>
    @else
        {{-- Info Requirements (read-only text) --}}
        @if($infoRequirements->isNotEmpty())
        <div class="event-requirements__block">
            <h3 class="event-requirements__heading">
                <img src="{{ asset('assets/icons/info-circle.svg') }}" alt="" class="event-requirements__icon">
                Information
            </h3>
            <ul class="event-requirements__list">
                @foreach($infoRequirements as $item)
                <li>{{ $item->title }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Tech Stack & Tools (read-only, grouped by category) --}}
        @if($techRequirements->isNotEmpty())
        <div class="event-requirements__block">
            <h3 class="event-requirements__heading">
                <img src="{{ asset('assets/icons/code-icon.svg') }}" alt="" class="event-requirements__icon">
                Tech Stack & Tools
            </h3>
            <div class="event-requirements__tech">
                @foreach(['tools', 'language', 'framework', 'database', 'other'] as $category)
                    @if($techRequirements->has($category) && $techRequirements[$category]->isNotEmpty())
                    <div class="event-requirements__tech-group">
                        <span class="event-requirements__tech-label">{{ ucfirst($category) }}:</span>
                        <div class="event-requirements__tags">
                            @foreach($techRequirements[$category] as $item)
                            @php
                                $iconUrl = \App\Support\TechIconResolver::url($item->title, $item->icon ?? null, $category);
                            @endphp
                            <span class="event-requirements__tag">
                                <span class="event-requirements__tag-icon">
                                    <img src="{{ $iconUrl }}" alt="" class="event-requirements__tag-img">
                                </span>
                                <span class="event-requirements__tag-text">{{ $item->title }}</span>
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Checklist (user-checkable if joined) --}}
        @if($checklistRequirements->isNotEmpty())
        <div class="event-requirements__block">
            <h3 class="event-requirements__heading">
                <img src="{{ asset('assets/icons/check-circle.svg') }}" alt="" class="event-requirements__icon">
                Preparation Checklist
            </h3>
            
            @if($isRegistered)
                {{-- Interactive checklist for joined users --}}
                <ul class="event-requirements__checklist" data-checklist>
                    @foreach($checklistRequirements as $item)
                    <li>
                        <label class="event-requirements__check-label">
                            <input 
                                type="checkbox" 
                                class="event-requirements__checkbox-input"
                                data-requirement-id="{{ $item->id }}"
                                data-toggle-url="{{ route('events.checklist.toggle', [$event, $item]) }}"
                                {{ $userChecks->contains($item->id) ? 'checked' : '' }}
                            >
                            <span class="event-requirements__checkbox-custom"></span>
                            <span class="event-requirements__check-text {{ $userChecks->contains($item->id) ? 'is-checked' : '' }}">
                                {{ $item->title }}
                            </span>
                        </label>
                    </li>
                    @endforeach
                </ul>
            @else
                {{-- Read-only checklist with join prompt --}}
                <ul class="event-requirements__checklist event-requirements__checklist--disabled">
                    @foreach($checklistRequirements as $item)
                    <li>
                        <span class="event-requirements__checkbox-custom"></span>
                        <span class="event-requirements__check-text">{{ $item->title }}</span>
                    </li>
                    @endforeach
                </ul>
                <p class="event-requirements__join-prompt">
                    <img src="{{ asset('assets/icons/lock.svg') }}" alt="" class="event-requirements__prompt-icon">
                    Join event untuk mulai checklist persiapan.
                </p>
            @endif
        </div>
        @endif
    @endif
</div>
