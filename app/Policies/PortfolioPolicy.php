<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\User;

class PortfolioPolicy
{
    /**
     * Determine if the user can view any portfolios.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the portfolio.
     */
    public function view(User $user, Portfolio $portfolio): bool
    {
        // Public portfolios can be viewed by anyone
        if ($portfolio->is_published) {
            return true;
        }

        // Draft portfolios can only be viewed by owner
        return $user->id === $portfolio->user_id;
    }

    /**
     * Determine if the user can create portfolios.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the portfolio.
     */
    public function update(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }

    /**
     * Determine if the user can delete the portfolio.
     */
    public function delete(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }

    /**
     * Determine if the user can publish the portfolio.
     */
    public function publish(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }

    /**
     * Determine if the user can unpublish the portfolio.
     */
    public function unpublish(User $user, Portfolio $portfolio): bool
    {
        return $user->id === $portfolio->user_id;
    }
}
