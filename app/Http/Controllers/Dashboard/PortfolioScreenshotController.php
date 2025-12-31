<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PortfolioScreenshot;
use App\Support\Traits\JsonResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortfolioScreenshotController extends Controller
{
    use JsonResponses;

    /**
     * Delete a screenshot.
     */
    public function destroy(PortfolioScreenshot $screenshot)
    {
        // Authorization check via portfolio ownership
        if ($screenshot->portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete file from storage
        if ($screenshot->path) {
            Storage::disk('public')->delete($screenshot->path);
        }

        $screenshot->delete();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Screenshot berhasil dihapus.');
        }

        return back()->with('success', 'Screenshot berhasil dihapus.');
    }
}
