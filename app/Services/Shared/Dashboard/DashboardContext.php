<?php

namespace App\Services\Shared\Dashboard;

use Illuminate\Http\Request;

/**
 * Simple DTO encapsulating dashboard request parameters.
 * 
 * Defaults match exact behavior from existing controller:
 * - tab defaults to 'overview'
 * - filter defaults to 'upcoming'
 * - section defaults to 'events'
 * - subtab defaults based on tab (mentor='participants', admin='edit')
 */
class DashboardContext
{
    public function __construct(
        public readonly string $tab,
        public readonly ?string $filter,
        public readonly ?string $status,
        public readonly ?string $section,
        public readonly ?string $eventSlug,
        public readonly ?string $subtab,
    ) {
    }

    /**
     * Build context from request with defaults matching existing behavior.
     */
    public static function fromRequest(Request $request): self
    {
        $tab = $request->get('tab', 'overview');

        return new self(
            tab: $tab,
            filter: $request->get('filter', 'upcoming'),
            status: $request->get('status'),
            section: $request->get('section', 'events'),
            eventSlug: $request->get('event'),
            subtab: $request->get('subtab') ?? match ($tab) {
                'mentor' => 'participants',
                'admin' => 'edit',
                default => null,
            },
        );
    }

    /**
     * Check if event detail view is requested (event slug provided).
     */
    public function hasEventDetail(): bool
    {
        return $this->eventSlug !== null;
    }
}
