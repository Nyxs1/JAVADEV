<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\OtpService;
use App\Services\Auth\Actions\CreateUser;
use App\Http\Support\Traits\JsonResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    use JsonResponses;

    public function __construct(
        private OtpService $otpService,
        private CreateUser $createUser
    ) {
    }

    public function show()
    {
        return view('auth.register');
    }

    public function sendCode(SendOtpRequest $request)
    {
        try {
            $email = $request->validated()['email'];
            $result = $this->otpService->sendOtp($email);

            if (!$result['success']) {
                return $this->jsonTooManyRequests($result['message'], $result['retry_after'] ?? 0);
            }

            return $this->jsonSuccess($result['message'], array_filter([
                'retry_after' => $result['retry_after'] ?? null,
                'expiry_minutes' => $result['expiry_minutes'] ?? null,
                'otp_code' => $result['otp_code'] ?? null,
            ]));

        } catch (\Exception $e) {
            Log::error('Error in sendCode: ' . $e->getMessage());
            return $this->jsonServerError();
        }
    }

    public function verifyCode(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            Log::info('[Register] Starting verification for: ' . $data['email']);

            // Verify OTP
            $otpResult = $this->otpService->verify($data['email'], $data['verification_code']);
            Log::info('[Register] OTP result: ' . json_encode($otpResult));

            if (!$otpResult['success']) {
                return $this->jsonError($otpResult['message'], [], 400);
            }

            // Create user
            Log::info('[Register] Creating user...');
            $user = $this->createUser->execute([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
            Log::info('[Register] User created with ID: ' . $user->id);

            // Login user
            Auth::login($user);
            Log::info('[Register] User logged in, redirecting to onboarding');

            return $this->jsonSuccess('Registration successful! Redirecting to onboarding...', [
                'redirect_url' => route('onboarding.index'),
            ]);

        } catch (\Exception $e) {
            Log::error('[Register] Exception: ' . $e->getMessage());
            Log::error('[Register] Stack trace: ' . $e->getTraceAsString());
            return $this->jsonError('Registration failed: ' . $e->getMessage(), [], 500);
        }
    }
}
