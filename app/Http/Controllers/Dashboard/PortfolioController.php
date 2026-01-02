<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PortfolioUpsertRequest;
use App\Models\Portfolio;
use App\Services\Portfolio\PortfolioMediaService;
use App\Services\Portfolio\PortfolioUpsertService;
use App\Http\Support\Traits\JsonResponses;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    use JsonResponses;

    public function __construct(
        private PortfolioUpsertService $upsertService,
        private PortfolioMediaService $mediaService
    ) {
    }

    /**
     * Display portfolio list in dashboard.
     */
    public function index()
    {
        $portfolios = Auth::user()->portfolios()
            ->with(['evidences', 'builtFromCourse', 'screenshots'])
            ->get();
        $userCourses = Auth::user()->userCourses()->get();

        return view('pages.dashboard.portfolio.index', [
            'portfolios' => $portfolios,
            'userCourses' => $userCourses,
        ]);
    }

    /**
     * Store a new portfolio.
     */
    public function store(PortfolioUpsertRequest $request)
    {
        $portfolio = $this->upsertService->store(
            $request->validated(),
            Auth::user()
        );

        if ($request->wantsJson()) {
            return $this->jsonSuccess('Portfolio berhasil dibuat.', [
                'portfolio' => $portfolio,
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil dibuat.');
    }

    /**
     * Upsert (create or update) a portfolio via modal wizard.
     */
    public function upsert(PortfolioUpsertRequest $request)
    {
        $portfolio = $request->getPortfolio();

        $result = $this->upsertService->upsert(
            $request->validated(),
            Auth::user(),
            $portfolio
        );

        // Determine message based on publish state
        $isPublishing = $request->boolean('publish_now');
        $message = $isPublishing
            ? 'Portfolio berhasil disimpan dan dipublikasikan.'
            : 'Portfolio berhasil disimpan sebagai draft.';

        if ($request->wantsJson()) {
            return $this->jsonSuccess($message, [
                'portfolio' => $result,
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', $message);
    }

    /**
     * Update a portfolio.
     */
    public function update(PortfolioUpsertRequest $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $result = $this->upsertService->update($request->validated(), $portfolio);

        if ($request->wantsJson()) {
            return $this->jsonSuccess('Portfolio berhasil diperbarui.', [
                'portfolio' => $result,
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil diperbarui.');
    }

    /**
     * Delete a portfolio.
     */
    public function destroy(Portfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);

        $this->upsertService->delete($portfolio);

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Portfolio berhasil dihapus.');
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio berhasil dihapus.');
    }

    /**
     * Publish a portfolio to public profile.
     */
    public function publish(Portfolio $portfolio)
    {
        $this->authorize('publish', $portfolio);

        $portfolio->publish();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Portfolio dipublikasikan. Sekarang terlihat di profil publikmu.', [
                'is_published' => true,
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio dipublikasikan.');
    }

    /**
     * Unpublish a portfolio (hide from public profile).
     */
    public function unpublish(Portfolio $portfolio)
    {
        $this->authorize('unpublish', $portfolio);

        $portfolio->unpublish();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Portfolio disembunyikan dari profil publik.', [
                'is_published' => false,
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', 'Portfolio disembunyikan.');
    }
}
