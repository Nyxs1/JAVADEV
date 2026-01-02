<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PortfolioScreenshot;
use App\Services\Portfolio\PortfolioMediaService;
use App\Http\Support\Traits\JsonResponses;

class PortfolioScreenshotController extends Controller
{
    use JsonResponses;

    public function __construct(
        private PortfolioMediaService $mediaService
    ) {
    }

    /**
     * Delete a screenshot.
     */
    public function destroy(PortfolioScreenshot $screenshot)
    {
        $this->authorize('delete', $screenshot);

        $this->mediaService->deleteScreenshot($screenshot);

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Screenshot berhasil dihapus.');
        }

        return back()->with('success', 'Screenshot berhasil dihapus.');
    }
}
