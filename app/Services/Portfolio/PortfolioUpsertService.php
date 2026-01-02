<?php

namespace App\Services\Portfolio;

use App\Models\ItemEvidence;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class PortfolioUpsertService
{
    public function __construct(
        private PortfolioMediaService $mediaService
    ) {
    }

    /**
     * Create or update a portfolio.
     *
     * @param array $payload Validated request data
     * @param User $actor The user performing the action
     * @param Portfolio|null $portfolio Existing portfolio for update, null for create
     * @return Portfolio
     */
    public function upsert(array $payload, User $actor, ?Portfolio $portfolio = null): Portfolio
    {
        // Determine source type and id
        $sourceType = null;
        $sourceId = null;

        if (!empty($payload['source_course_id'])) {
            $course = $actor->userCourses()->find($payload['source_course_id']);
            if ($course) {
                $sourceType = 'course';
                $sourceId = $course->id;
            }
        }

        // Prepare data
        $data = [
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'readme_md' => $payload['readme_md'] ?? null,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ];

        // Handle cover upload
        if (isset($payload['cover']) && $payload['cover'] instanceof UploadedFile) {
            $oldPath = $portfolio?->cover_path;
            $data['cover_path'] = $this->mediaService->uploadCover($payload['cover'], $oldPath);
        }

        // Create or update portfolio
        if ($portfolio) {
            $portfolio->update($data);
        } else {
            $data['is_published'] = false;
            $portfolio = $actor->portfolios()->create($data);
        }

        // Handle screenshots upload
        if (isset($payload['screenshots']) && is_array($payload['screenshots'])) {
            $this->mediaService->uploadScreenshots($payload['screenshots'], $portfolio);
        }

        // Handle new evidences
        if (!empty($payload['new_evidences'])) {
            $this->createEvidences($payload['new_evidences'], $portfolio, $actor);
        }

        // Handle publish
        if (!empty($payload['publish_now']) && $payload['publish_now']) {
            $portfolio->publish();
        }

        return $portfolio->fresh(['screenshots', 'evidences']);
    }

    /**
     * Store a new portfolio.
     *
     * @param array $payload Validated request data
     * @param User $actor The user performing the action
     * @return Portfolio
     */
    public function store(array $payload, User $actor): Portfolio
    {
        return $this->upsert($payload, $actor, null);
    }

    /**
     * Update an existing portfolio.
     *
     * @param array $payload Validated request data
     * @param Portfolio $portfolio The portfolio to update
     * @return Portfolio
     */
    public function update(array $payload, Portfolio $portfolio): Portfolio
    {
        $data = [
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
        ];

        // Handle cover upload
        if (isset($payload['cover']) && $payload['cover'] instanceof UploadedFile) {
            $oldPath = $portfolio->cover_path;
            $data['cover_path'] = $this->mediaService->uploadCover($payload['cover'], $oldPath);
        }

        $portfolio->update($data);

        return $portfolio->fresh();
    }

    /**
     * Delete a portfolio and all its media.
     *
     * @param Portfolio $portfolio The portfolio to delete
     * @return void
     */
    public function delete(Portfolio $portfolio): void
    {
        // Delete all media
        $this->mediaService->deleteAllMedia($portfolio);

        // Delete portfolio (will cascade delete screenshots and evidences)
        $portfolio->delete();
    }

    /**
     * Create evidences for a portfolio.
     *
     * @param array $evidences Array of evidence data
     * @param Portfolio $portfolio The portfolio to attach evidences to
     * @param User $actor The user creating the evidences
     * @return void
     */
    private function createEvidences(array $evidences, Portfolio $portfolio, User $actor): void
    {
        foreach ($evidences as $ev) {
            if (!empty($ev['value'])) {
                ItemEvidence::create([
                    'user_id' => $actor->id,
                    'item_type' => 'portfolio',
                    'item_id' => $portfolio->id,
                    'type' => $ev['type'],
                    'label' => $ev['label'] ?? null,
                    'value' => $ev['value'],
                    'is_public' => true,
                ]);
            }
        }
    }
}
