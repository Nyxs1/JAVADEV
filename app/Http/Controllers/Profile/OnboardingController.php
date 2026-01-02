<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\StoreOnboardingRequest;
use App\Services\Onboarding\SaveOnboardingData;
use App\Http\Support\FlashMessage;
use App\Http\Support\Traits\JsonResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    use JsonResponses;

    public function __construct(
        private SaveOnboardingData $saveOnboardingData
    ) {
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasCompletedOnboarding()) {
            return redirect('/profile')
                ->with(FlashMessage::INFO, 'Onboarding already completed.');
        }

        return view('pages.onboarding.index', compact('user'));
    }

    public function store(StoreOnboardingRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // DEBUG: Log what we received
        \Log::info("[Onboarding Controller] Received data:", [
            'has_cropped_avatar' => $request->hasCroppedAvatar(),
            'cropped_avatar_length' => strlen($data['cropped_avatar'] ?? ''),
            'has_profile_picture' => $request->hasFile('profile_picture'),
        ]);

        try {
            DB::beginTransaction();
            $croppedAvatar = $request->hasCroppedAvatar()
                ? $data['cropped_avatar']
                : null;

            $this->saveOnboardingData->execute(
                $user,
                $data,
                $croppedAvatar,
                $request->file('profile_picture'),
                $request->wantsMentorRole()
            );

            DB::commit();

            // refresh biar avatar_focus terbaru kepake
            $user->refresh();

            // AJAX response (WAJIB buat sync navbar)
            if ($request->expectsJson()) {
                return $this->jsonSuccess(
                    'Onboarding complete!',
                    [
                        'redirect_url' => route('profile.show', $user->username),
                        'avatar_url' => $user->avatar_url,
                        'avatar_version' => $user->updated_at?->timestamp ?? time(),
                        'avatar_style' => $user->avatar_style,
                        'avatar_focus' => $user->getAvatarFocusWithDefaults(),
                    ]
                );
            }

            return redirect()
                ->route('profile.show', $user->username)
                ->with(FlashMessage::SUCCESS, 'Onboarding complete!');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[Onboarding] FAILED', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson()) {
                return $this->jsonError('Failed saving onboarding.');
            }

            return back()->withErrors(['submit' => 'Failed saving onboarding.']);
        }
    }
}
