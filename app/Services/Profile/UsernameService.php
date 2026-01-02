<?php

namespace App\Services\Profile;

use App\Models\User;

/**
 * Handles username availability checking and updates.
 * 
 * Responsibilities:
 * - Validate username format
 * - Check availability against database
 * - Update username
 */
class UsernameService
{
    private const MIN_LENGTH = 3;
    private const MAX_LENGTH = 20;
    private const PATTERN = '/^[a-zA-Z0-9_]+$/';

    /**
     * Check if username is available for registration/update.
     *
     * @param string $username Username to check
     * @param User|null $currentUser Current user (to allow their own username)
     * @return array{available: bool, reason?: string, same?: bool}
     */
    public function checkAvailability(string $username, ?User $currentUser = null): array
    {
        $username = $this->normalize($username);

        // Validate format
        $formatError = $this->validateFormat($username);
        if ($formatError) {
            return ['available' => false, 'reason' => $formatError];
        }

        // Check if same as current
        if ($currentUser && $currentUser->username === $username) {
            return ['available' => true, 'same' => true];
        }

        // Check if taken
        if (User::where('username', $username)->exists()) {
            return ['available' => false, 'reason' => 'Username is already taken'];
        }

        return ['available' => true];
    }

    /**
     * Update user's username.
     *
     * @param User $user User to update
     * @param string $newUsername New username
     * @return array{success: bool, message: string}
     */
    public function update(User $user, string $newUsername): array
    {
        $newUsername = $this->normalize($newUsername);

        // Validate format
        $formatError = $this->validateFormat($newUsername);
        if ($formatError) {
            return ['success' => false, 'message' => $formatError];
        }

        // Check if same as current
        if ($user->username === $newUsername) {
            return ['success' => false, 'message' => 'This is already your username'];
        }

        // Check if taken by another user
        $isTaken = User::where('username', $newUsername)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($isTaken) {
            return ['success' => false, 'message' => 'Username is already taken'];
        }

        // Update
        $user->username = $newUsername;
        $user->save();

        return ['success' => true, 'message' => 'Username updated successfully'];
    }

    /**
     * Normalize username (lowercase, trimmed).
     */
    private function normalize(string $username): string
    {
        return strtolower(trim($username));
    }

    /**
     * Validate username format.
     *
     * @return string|null Error message or null if valid
     */
    private function validateFormat(string $username): ?string
    {
        if (strlen($username) < self::MIN_LENGTH) {
            return 'Username must be at least ' . self::MIN_LENGTH . ' characters';
        }

        if (strlen($username) > self::MAX_LENGTH) {
            return 'Username must be ' . self::MAX_LENGTH . ' characters or less';
        }

        if (!preg_match(self::PATTERN, $username)) {
            return 'Only letters, numbers, and underscores allowed';
        }

        return null;
    }
}
