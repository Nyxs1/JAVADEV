<?php

namespace App\Http\Controllers;

use App\Http\Requests\Onboarding\StoreOnboardingRequest;
use App\Actions\Onboarding\SaveOnboardingData;
use App\Support\FlashMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    public function __construct(
        private SaveOnboardingData $saveOnboardingData
    ) {
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasCompletedOnboarding()) {
            return redirect('/profile')->with(FlashMessage::INFO, 'Onboarding already completed.');
        }

        return view('pages.onboarding.index', compact('user'));
    }

    public function store(StoreOnboardingRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $this->saveOnboardingData->execute(
                $user,
                $data,
                $request->hasCroppedAvatar() ? $data['cropped_avatar'] : null,
                $request->file('profile_picture'),
                $request->wantsMentorRole()
            );

            DB::commit();

            return redirect('/profile')->with(FlashMessage::SUCCESS, 'Onboarding complete! Welcome to Java Developer Group.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Onboarding error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['submit' => 'An error occurred while saving data. Please try again.']);
        }
    }
}
