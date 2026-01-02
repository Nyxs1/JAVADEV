<?php

namespace App\Policies;

use App\Models\PortfolioScreenshot;
use App\Models\User;

class PortfolioScreenshotPolicy
{
    /**
     * Determine if the user can delete the screenshot.
     */
    public function delete(User $user, PortfolioScreenshot $screenshot): bool
    {
        // User can delete screenshot if they own the portfolio
        return $user->id === $screenshot->portfolio->user_id;
    }
}
