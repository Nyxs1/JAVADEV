<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ItemEvidence;
use App\Models\Portfolio;
use App\Models\UserCourse;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemEvidenceController extends Controller
{
    use JsonResponses;

    /**
     * Store a new evidence.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type' => 'required|in:portfolio,user_course',
            'item_id' => 'required|integer',
            'type' => 'required|in:github,link,demo,pdf',
            'label' => 'nullable|string|max:100',
            'value' => 'required|url|max:500',
            'is_public' => 'boolean',
        ]);

        // Verify ownership of the item
        $item = $this->getItem($validated['item_type'], $validated['item_id']);
        if (!$item || $item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $evidence = ItemEvidence::create([
            'user_id' => Auth::id(),
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
            'type' => $validated['type'],
            'label' => $validated['label'] ?? null,
            'value' => $validated['value'],
            'is_public' => $validated['is_public'] ?? true,
        ]);

        if ($request->wantsJson()) {
            return $this->jsonSuccess('Evidence berhasil ditambahkan.', [
                'evidence' => $evidence,
            ]);
        }

        return back()->with('success', 'Evidence berhasil ditambahkan.');
    }

    /**
     * Delete an evidence.
     */
    public function destroy(ItemEvidence $evidence)
    {
        // Authorization check
        if ($evidence->user_id !== Auth::id()) {
            abort(403);
        }

        $evidence->delete();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Evidence berhasil dihapus.');
        }

        return back()->with('success', 'Evidence berhasil dihapus.');
    }

    /**
     * Get the item by type and id.
     */
    private function getItem(string $type, int $id)
    {
        return match ($type) {
            'portfolio' => Portfolio::find($id),
            'user_course' => UserCourse::find($id),
            default => null,
        };
    }
}
