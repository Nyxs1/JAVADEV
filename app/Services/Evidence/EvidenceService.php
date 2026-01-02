<?php

namespace App\Services\Evidence;

use App\Models\ItemEvidence;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\UserCourse;

class EvidenceService
{
    /**
     * Store a new evidence.
     *
     * @param array $payload Validated request data
     * @param User $actor The user creating the evidence
     * @return ItemEvidence
     */
    public function store(array $payload, User $actor): ItemEvidence
    {
        return ItemEvidence::create([
            'user_id' => $actor->id,
            'item_type' => $payload['item_type'],
            'item_id' => $payload['item_id'],
            'type' => $payload['type'],
            'label' => $payload['label'] ?? null,
            'value' => $payload['value'],
            'is_public' => $payload['is_public'] ?? true,
        ]);
    }

    /**
     * Delete an evidence.
     *
     * @param ItemEvidence $evidence The evidence to delete
     * @return void
     */
    public function delete(ItemEvidence $evidence): void
    {
        $evidence->delete();
    }

    /**
     * Get the item by type and id.
     *
     * @param string $type The item type (portfolio or user_course)
     * @param int $id The item ID
     * @return Portfolio|UserCourse|null
     */
    public function getItem(string $type, int $id): mixed
    {
        return match ($type) {
            'portfolio' => Portfolio::find($id),
            'user_course' => UserCourse::find($id),
            default => null,
        };
    }

    /**
     * Check if user owns the item.
     *
     * @param string $type The item type
     * @param int $id The item ID
     * @param User $user The user to check ownership for
     * @return bool
     */
    public function userOwnsItem(string $type, int $id, User $user): bool
    {
        $item = $this->getItem($type, $id);
        return $item && $item->user_id === $user->id;
    }
}
