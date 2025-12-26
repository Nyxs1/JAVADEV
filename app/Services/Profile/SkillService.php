<?php

namespace App\Services\Profile;

use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Support\Str;

/**
 * Handles user skill CRUD operations.
 * 
 * Responsibilities:
 * - Add new skills (with duplicate check)
 * - Update skill levels
 * - Remove skills
 */
class SkillService
{
    /**
     * Add a new skill to user's profile.
     *
     * @param User $user
     * @param string $techName Skill name (e.g., "PHP", "Laravel")
     * @param int $level Skill level (1-4)
     * @return array{success: bool, message: string, skill?: UserSkill}
     */
    public function addSkill(User $user, string $techName, int $level): array
    {
        $techSlug = Str::slug($techName);

        // Check for duplicate
        if ($user->skills()->where('tech_slug', $techSlug)->exists()) {
            return [
                'success' => false,
                'message' => 'This skill already exists.',
            ];
        }

        $skill = $user->skills()->create([
            'tech_slug' => $techSlug,
            'tech_name' => $techName,
            'level' => $level,
        ]);

        return [
            'success' => true,
            'message' => 'Skill added successfully.',
            'skill' => $skill,
        ];
    }

    /**
     * Update skill level.
     *
     * @param User $user
     * @param int $skillId
     * @param int $newLevel
     * @return array{success: bool, message: string}
     */
    public function updateLevel(User $user, int $skillId, int $newLevel): array
    {
        $skill = $user->skills()->find($skillId);

        if (!$skill) {
            return [
                'success' => false,
                'message' => 'Skill not found.',
            ];
        }

        $skill->update(['level' => $newLevel]);

        return [
            'success' => true,
            'message' => 'Skill updated successfully.',
        ];
    }

    /**
     * Remove a skill from user's profile.
     *
     * @param User $user
     * @param int $skillId
     * @return array{success: bool, message: string}
     */
    public function remove(User $user, int $skillId): array
    {
        $skill = $user->skills()->find($skillId);

        if (!$skill) {
            return [
                'success' => false,
                'message' => 'Skill not found.',
            ];
        }

        $skill->delete();

        return [
            'success' => true,
            'message' => 'Skill removed successfully.',
        ];
    }
}
