<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\Actions\AttemptLogin;
use App\Services\Auth\Actions\ResetUserPassword;
use App\Models\User;
use App\Http\Support\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        private AttemptLogin $attemptLogin,
        private ResetUserPassword $resetUserPassword
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $success = $this->attemptLogin->execute(
            $data['login'],
            $data['password'],
            $request->boolean('remember')
        );

        if ($success) {
            $request->session()->regenerate();

            $user = Auth::user();
            $redirectTo = $user->hasCompletedOnboarding()
                ? route('users.dashboard', $user->username)
                : route('onboarding.index');

            return redirect()->intended($redirectTo)
                ->with(FlashMessage::SUCCESS, 'Login successful');
        }

        return back()
            ->withErrors(['auth' => 'Invalid email/username or password.'])
            ->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with(FlashMessage::LOGOUT_SUCCESS, 'Logout berhasil');
    }

    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $email = $request->validated()['email'];

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => "We can't find a user with that email address."]);
        }

        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with([FlashMessage::STATUS => 'If the email exists, we have sent a password reset link.'])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $status = $this->resetUserPassword->execute($data);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with(FlashMessage::STATUS, 'Password has been reset. Please log in.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
