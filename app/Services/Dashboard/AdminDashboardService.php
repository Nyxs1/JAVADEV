<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\User;
use App\Services\Shared\Dashboard\DashboardContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handles admin tab data loading.
 */
class AdminDashboardService
{
    /**
     * Load admin tab data based on context.
     */
    public function load(DashboardContext $ctx, Request $request): array
    {
        if ($ctx->section === 'finalization') {
            return $this->loadFinalizationData();
        }

        $data = ['adminSection' => $ctx->section];

        if ($ctx->hasEventDetail()) {
            return array_merge($data, $this->loadEventDetail(
                Event::where('slug', $ctx->eventSlug)->firstOrFail(),
                $ctx->subtab
            ));
        }

        return array_merge($data, $this->loadEventsList($request));
    }

    /**
     * Load admin events list with filtering.
     */
    public function loadEventsList(Request $request): array
    {
        $query = Event::query()
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'mentors as mentors_count',
            ]);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $this->applyStatusFilter($query, $status);
        }

        return [
            'adminEvents' => $query->orderBy('start_at', 'desc')->paginate(15),
            'adminCounts' => $this->getAdminEventCounts(),
        ];
    }

    /**
     * Load admin event detail view.
     */
    public function loadEventDetail(Event $event, string $subtab): array
    {
        $data = [
            'adminEvent' => $event,
            'adminSubtab' => $subtab,
        ];

        match ($subtab) {
            'mentors' => $data = array_merge($data, $this->loadEventMentorsData($event)),
            'requirements' => $data = array_merge($data, $this->loadEventRequirementsData($event)),
            'reviews' => $data = array_merge($data, $this->loadEventReviewsData($event)),
            default => null,
        };

        return $data;
    }

    /**
     * Load finalization section data.
     */
    public function loadFinalizationData(): array
    {
        return [
            'adminSection' => 'finalization',
            'pendingEvents' => $this->getPendingFinalizationEvents(),
            'finalizedEvents' => $this->getRecentlyFinalizedEvents(),
        ];
    }

    /**
     * Delete event cover image from storage.
     */
    public function deleteEventCoverImage(Event $event): void
    {
        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function getAdminEventCounts(): array
    {
        return [
            'all' => Event::count(),
            'draft' => Event::where('status', 'draft')->count(),
            'upcoming' => Event::upcoming()->count(),
            'ongoing' => Event::ongoing()->count(),
            'ended' => Event::ended()->count(),
        ];
    }

    private function loadEventMentorsData(Event $event): array
    {
        $event->load(['mentors.user']);

        return [
            'eventMentors' => $event->mentors,
            'availableMentors' => User::whereHas('role', fn($q) => $q->whereIn('name', ['mentor', 'admin']))
                ->whereNotIn('id', $event->mentors->pluck('user_id'))
                ->orderBy('name')
                ->get(),
        ];
    }

    private function loadEventRequirementsData(Event $event): array
    {
        $requirements = $event->requirementItems()->get();

        return [
            'infoRequirements' => $requirements->where('type', 'info')->values(),
            'checklistRequirements' => $requirements->where('type', 'checklist')->values(),
            'techRequirements' => $requirements->where('type', 'tech')->groupBy('category'),
        ];
    }

    private function loadEventReviewsData(Event $event): array
    {
        $reviews = $event->feedback()->global();

        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = (clone $reviews)->where('rating', $i)->count();
        }

        return [
            'eventReviews' => $reviews->with('fromUser')->latest()->paginate(20),
            'avgRating' => $event->feedback()->global()->avg('rating'),
            'reviewCount' => $event->feedback()->global()->count(),
            'ratingDistribution' => $ratingDistribution,
        ];
    }

    private function getPendingFinalizationEvents()
    {
        return Event::ended()
            ->whereNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as present_count' => fn($q) => $q->where('attendance_status', 'present'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('end_at', 'desc')
            ->get();
    }

    private function getRecentlyFinalizedEvents()
    {
        return Event::whereNotNull('finalized_at')
            ->withCount([
                'participants as registered_count' => fn($q) => $q->where('registration_status', 'registered'),
                'participants as completed_count' => fn($q) => $q->where('completion_status', 'completed'),
            ])
            ->orderBy('finalized_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function applyStatusFilter($query, string $status): void
    {
        match ($status) {
            'upcoming' => $query->upcoming(),
            'ongoing' => $query->ongoing(),
            'ended' => $query->ended(),
            'draft' => $query->where('status', 'draft'),
            'cancelled' => $query->where('status', 'cancelled'),
            default => null,
        };
    }
}
