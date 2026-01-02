<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\EvidenceStoreRequest;
use App\Models\ItemEvidence;
use App\Services\Evidence\EvidenceService;
use App\Http\Support\Traits\JsonResponses;
use Illuminate\Support\Facades\Auth;

class EvidenceController extends Controller
{
    use JsonResponses;

    public function __construct(
        private EvidenceService $evidenceService
    ) {
    }

    /**
     * Store a new evidence.
     */
    public function store(EvidenceStoreRequest $request)
    {
        // Authorization is handled by EvidenceStoreRequest::authorize()

        $evidence = $this->evidenceService->store(
            $request->validated(),
            Auth::user()
        );

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
        $this->authorize('delete', $evidence);

        $this->evidenceService->delete($evidence);

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Evidence berhasil dihapus.');
        }

        return back()->with('success', 'Evidence berhasil dihapus.');
    }
}
