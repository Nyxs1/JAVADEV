<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Models\PortfolioScreenshot;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    use JsonResponses;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover' => 'nullable|image|max:2048',
            'source_course_id' => 'nullable|integer|exists:user_courses,id',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('portfolios', 'public');
        }

        // Determine source type and id
        $sourceType = null;
        $sourceId = null;
        if (!empty($validated['source_course_id'])) {
            // Verify ownership of the course
            $course = Auth::user()->userCourses()->find($validated['source_course_id']);
            if ($course) {
                $sourceType = 'course';
                $sourceId = $course->id;
            }
        }

        $portfolio = Auth::user()->portfolios()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cover_path' => $coverPath,
            'is_published' => false, // Starts as draft
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

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
    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'portfolio_id' => 'nullable|integer|exists:portfolios,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'readme_md' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'screenshots' => 'nullable|array|max:10',
            'screenshots.*' => 'image|max:4096',
            'source_course_id' => 'nullable|integer|exists:user_courses,id',
            'publish_now' => 'nullable|boolean',
            'agree_publish' => 'nullable|accepted',
            // New evidence fields (inline form)
            'new_evidences' => 'nullable|array|max:10',
            'new_evidences.*.type' => 'required_with:new_evidences|in:github,link,demo,pdf',
            'new_evidences.*.label' => 'nullable|string|max:100',
            'new_evidences.*.value' => 'required_with:new_evidences|url|max:500',
        ]);

        // If publish_now is set, require agree_publish
        if (!empty($validated['publish_now']) && $validated['publish_now']) {
            $request->validate([
                'agree_publish' => 'required|accepted',
            ], [
                'agree_publish.required' => 'Kamu harus menyetujui ketentuan sebelum publish.',
                'agree_publish.accepted' => 'Kamu harus menyetujui ketentuan sebelum publish.',
            ]);
        }

        // Load or create portfolio
        $portfolio = null;
        if (!empty($validated['portfolio_id'])) {
            $portfolio = Portfolio::find($validated['portfolio_id']);
            if (!$portfolio || $portfolio->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
        }

        // Determine source type and id
        $sourceType = null;
        $sourceId = null;
        if (!empty($validated['source_course_id'])) {
            $course = Auth::user()->userCourses()->find($validated['source_course_id']);
            if ($course) {
                $sourceType = 'course';
                $sourceId = $course->id;
            }
        }

        // Prepare data
        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'readme_md' => $validated['readme_md'] ?? null,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ];

        // Handle cover upload
        if ($request->hasFile('cover')) {
            // Delete old cover if updating
            if ($portfolio && $portfolio->cover_path) {
                Storage::disk('public')->delete($portfolio->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('portfolios', 'public');
        }

        // Create or update portfolio
        if ($portfolio) {
            $portfolio->update($data);
        } else {
            $data['is_published'] = false;
            $portfolio = Auth::user()->portfolios()->create($data);
        }

        // Handle screenshots upload
        if ($request->hasFile('screenshots')) {
            $maxSortOrder = $portfolio->screenshots()->max('sort_order') ?? 0;
            foreach ($request->file('screenshots') as $index => $file) {
                $path = $file->store('portfolio-screenshots', 'public');
                $portfolio->screenshots()->create([
                    'path' => $path,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        }

        // Handle new evidences (inline form submission)
        if (!empty($validated['new_evidences'])) {
            foreach ($validated['new_evidences'] as $ev) {
                if (!empty($ev['value'])) {
                    \App\Models\ItemEvidence::create([
                        'user_id' => Auth::id(),
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

        // Handle publish
        if (!empty($validated['publish_now']) && $validated['publish_now']) {
            $portfolio->publish();
            $message = 'Portfolio berhasil disimpan dan dipublikasikan.';
        } else {
            $message = 'Portfolio berhasil disimpan sebagai draft.';
        }

        if ($request->wantsJson()) {
            return $this->jsonSuccess($message, [
                'portfolio' => $portfolio->fresh(['screenshots', 'evidences']),
            ]);
        }

        return redirect()->route('dashboard.portfolio.index')
            ->with('success', $message);
    }

    /**
     * Update a portfolio.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        // Authorization check
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('cover')) {
            // Delete old cover
            if ($portfolio->cover_path) {
                Storage::disk('public')->delete($portfolio->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('portfolios', 'public');
        }

        $portfolio->update($data);

        if ($request->wantsJson()) {
            return $this->jsonSuccess('Portfolio berhasil diperbarui.', [
                'portfolio' => $portfolio->fresh(),
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
        // Authorization check
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete cover if exists
        if ($portfolio->cover_path) {
            Storage::disk('public')->delete($portfolio->cover_path);
        }

        $portfolio->delete();

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
        // Authorization check
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

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
        // Authorization check
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

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
