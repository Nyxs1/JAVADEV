<?php

namespace App\Services\Portfolio;

use App\Models\Portfolio;
use App\Models\PortfolioScreenshot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PortfolioMediaService
{
    /**
     * Upload a cover image.
     *
     * @param UploadedFile $file The uploaded file
     * @param string|null $oldPath Path to delete after successful upload
     * @return string The new file path
     */
    public function uploadCover(UploadedFile $file, ?string $oldPath = null): string
    {
        // Delete old cover if exists
        if ($oldPath) {
            $this->deleteCover($oldPath);
        }

        return $file->store('portfolios', 'public');
    }

    /**
     * Upload multiple screenshots for a portfolio.
     *
     * @param array<UploadedFile> $files Array of uploaded files
     * @param Portfolio $portfolio The portfolio to attach screenshots to
     * @return void
     */
    public function uploadScreenshots(array $files, Portfolio $portfolio): void
    {
        $maxSortOrder = $portfolio->screenshots()->max('sort_order') ?? 0;

        foreach ($files as $index => $file) {
            $path = $file->store('portfolio-screenshots', 'public');
            $portfolio->screenshots()->create([
                'path' => $path,
                'sort_order' => $maxSortOrder + $index + 1,
            ]);
        }
    }

    /**
     * Delete a cover image from storage.
     *
     * @param string $path The file path to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteCover(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Delete a screenshot and its file.
     *
     * @param PortfolioScreenshot $screenshot The screenshot to delete
     * @return void
     */
    public function deleteScreenshot(PortfolioScreenshot $screenshot): void
    {
        if ($screenshot->path) {
            Storage::disk('public')->delete($screenshot->path);
        }

        $screenshot->delete();
    }

    /**
     * Delete all screenshots for a portfolio.
     *
     * @param Portfolio $portfolio The portfolio whose screenshots to delete
     * @return void
     */
    public function deleteAllScreenshots(Portfolio $portfolio): void
    {
        foreach ($portfolio->screenshots as $screenshot) {
            $this->deleteScreenshot($screenshot);
        }
    }

    /**
     * Delete all media (cover + screenshots) for a portfolio.
     *
     * @param Portfolio $portfolio The portfolio whose media to delete
     * @return void
     */
    public function deleteAllMedia(Portfolio $portfolio): void
    {
        if ($portfolio->cover_path) {
            $this->deleteCover($portfolio->cover_path);
        }

        $this->deleteAllScreenshots($portfolio);
    }
}
