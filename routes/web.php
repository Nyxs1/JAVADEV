<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;

Route::get('/', [HomeController::class, 'index'])->name('index');

/* ============================= */
/* AUTH LOGIC                    */
/* ============================= */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register/send-code', [RegisterController::class, 'sendCode'])->name('register.send-code');
Route::post('/register/verify-code', [RegisterController::class, 'verifyCode'])->name('register.verify-code');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes (Laravel Default)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token, \Illuminate\Http\Request $request) {
    return view('auth.reset-password', [
        'token' => $token,
        'email' => $request->query('email')
    ]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')->name('password.update');

/* ============================= */
/* ONBOARDING ROUTES             */
/* ============================= */
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');
});

/* ============================= */
/* EVENT ROUTES                  */
/* ============================= */
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventDetailController;
use App\Http\Controllers\EventRequirementsController;
use App\Http\Controllers\EventReviewController;
use App\Http\Controllers\EventChecklistController;
use App\Http\Controllers\EventParticipantController;
use App\Http\Controllers\MyEventsController;

// Public event listing
Route::get('/events', [EventController::class, 'index'])->name('events.index');

// Public event detail
Route::get('/events/{event:slug}', [EventDetailController::class, 'show'])->name('events.show');

// Protected event routes
Route::middleware('auth')->group(function () {
    // Join/Cancel
    Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');
    Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');

    // Check-in & Participation
    Route::post('/events/{event}/check-in', [EventParticipantController::class, 'checkIn'])->name('events.check-in');
    Route::post('/events/{event}/reflection', [EventParticipantController::class, 'submitReflection'])->name('events.reflection');
    Route::get('/events/{event}/certificate', [EventParticipantController::class, 'certificate'])->name('events.certificate');

    // Requirements edit
    Route::get('/events/{event}/requirements/edit', [EventRequirementsController::class, 'edit'])->name('events.requirements.edit');
    Route::put('/events/{event}/requirements', [EventRequirementsController::class, 'update'])->name('events.requirements.update');

    // Checklist toggle
    Route::post('/events/{event}/checklist/{requirement}/toggle', [EventChecklistController::class, 'toggle'])->name('events.checklist.toggle');

    // Reviews
    Route::post('/events/{event}/reviews', [EventReviewController::class, 'store'])->name('events.reviews.store');

    // My Events
    Route::get('/my/events', [MyEventsController::class, 'index'])->name('my.events');
});

/* ============================= */
/* USERS DASHBOARD ROUTES        */
/* ============================= */
use App\Http\Controllers\UsersDashboardController;

Route::middleware('auth')->name('users.')->group(function () {
    // Mentor action routes (requires mentor or admin role)
    Route::middleware(\App\Http\Middleware\EnsureMentor::class)->prefix('dashboard')->group(function () {
        Route::post('/events/{event:slug}/requirements', [UsersDashboardController::class, 'storeRequirement'])->name('requirements.store');
        Route::delete('/events/{event:slug}/requirements/{requirement}', [UsersDashboardController::class, 'destroyRequirement'])->name('requirements.destroy');
        Route::post('/events/{event:slug}/participants/{participant}/present', [UsersDashboardController::class, 'markPresent'])->name('participants.present');
        Route::post('/events/{event:slug}/participants/{participant}/completed', [UsersDashboardController::class, 'markCompleted'])->name('participants.completed');
    });

    // Admin action routes (requires admin role)
    Route::middleware(\App\Http\Middleware\EnsureAdmin::class)->prefix('dashboard')->group(function () {
        // Events CRUD
        Route::post('/events', [UsersDashboardController::class, 'storeEvent'])->name('events.store');
        Route::put('/events/{event:slug}', [UsersDashboardController::class, 'updateEvent'])->name('events.update');
        Route::delete('/events/{event:slug}', [UsersDashboardController::class, 'destroyEvent'])->name('events.destroy');

        // Event Mentors
        Route::post('/events/{event:slug}/mentors', [UsersDashboardController::class, 'storeMentor'])->name('mentors.store');
        Route::put('/events/{event:slug}/mentors/{mentor}', [UsersDashboardController::class, 'updateMentor'])->name('mentors.update');
        Route::delete('/events/{event:slug}/mentors/{mentor}', [UsersDashboardController::class, 'destroyMentor'])->name('mentors.destroy');

        // Admin Requirements
        Route::post('/admin/events/{event:slug}/requirements', [UsersDashboardController::class, 'storeRequirement'])->name('admin.requirements.store');
        Route::delete('/admin/events/{event:slug}/requirements/{requirement}', [UsersDashboardController::class, 'destroyRequirement'])->name('admin.requirements.destroy');

        // Reviews
        Route::delete('/events/{event:slug}/reviews/{review}', [UsersDashboardController::class, 'destroyReview'])->name('reviews.destroy');

        // Finalization
        Route::post('/events/{event:slug}/finalize', [UsersDashboardController::class, 'finalizeEvent'])->name('events.finalize');
        Route::post('/finalization/batch', [UsersDashboardController::class, 'runBatchFinalization'])->name('finalization.batch');
    });
});

/* ============================= */
/* DASHBOARD - PORTFOLIO         */
/* ============================= */
use App\Http\Controllers\Dashboard\PortfolioController;
use App\Http\Controllers\Dashboard\PortfolioScreenshotController;
use App\Http\Controllers\Dashboard\UserCourseController;
use App\Http\Controllers\Dashboard\ItemEvidenceController;

Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    // Portfolio CRUD
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::post('/portfolio/upsert', [PortfolioController::class, 'upsert'])->name('portfolio.upsert');
    Route::put('/portfolio/{portfolio}', [PortfolioController::class, 'update'])->name('portfolio.update');
    Route::delete('/portfolio/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    Route::post('/portfolio/{portfolio}/publish', [PortfolioController::class, 'publish'])->name('portfolio.publish');
    Route::post('/portfolio/{portfolio}/unpublish', [PortfolioController::class, 'unpublish'])->name('portfolio.unpublish');

    // Portfolio Screenshots
    Route::delete('/portfolio-screenshots/{screenshot}', [PortfolioScreenshotController::class, 'destroy'])->name('portfolio.screenshots.destroy');

    // Courses Management
    Route::get('/courses', [UserCourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [UserCourseController::class, 'storeDemoEnrollment'])->name('courses.store');
    Route::post('/courses/{userCourse}/publish', [UserCourseController::class, 'publish'])->name('courses.publish');
    Route::post('/courses/{userCourse}/unpublish', [UserCourseController::class, 'unpublish'])->name('courses.unpublish');

    // Evidence Management
    Route::post('/evidence', [ItemEvidenceController::class, 'store'])->name('evidence.store');
    Route::delete('/evidence/{evidence}', [ItemEvidenceController::class, 'destroy'])->name('evidence.destroy');
});


/* ============================= */
/* PROFILE ROUTES                */
/* ============================= */
Route::middleware('auth')->group(function () {
    // Redirect /profile to user's own profile
    Route::get('/profile', function () {
        return redirect()->route('profile.show', auth()->user()->username);
    })->name('profile.index');

    // Account Settings
    Route::get('/settings', [App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/settings/account', [App\Http\Controllers\ProfileController::class, 'updateAccount'])->name('profile.update-account');
    Route::post('/settings/check-username', [App\Http\Controllers\ProfileController::class, 'checkUsername'])->name('profile.check-username');

    // Username change (Instagram-style)
    Route::get('/settings/username/check', [App\Http\Controllers\ProfileController::class, 'checkUsernameAvailability'])->name('settings.username.check');
    Route::post('/settings/username', [App\Http\Controllers\ProfileController::class, 'updateUsername'])->name('settings.username.update');

    // Profile actions
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/role-request', [App\Http\Controllers\ProfileController::class, 'requestRoleChange'])->name('profile.role-request');
    Route::post('/profile/visibility', [App\Http\Controllers\ProfileController::class, 'updateVisibility'])->name('profile.visibility');
    Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/avatar-focus', [App\Http\Controllers\ProfileController::class, 'updateAvatarFocus'])->name('profile.avatar-focus');

    // Skills management
    Route::post('/profile/skills', [App\Http\Controllers\ProfileController::class, 'storeSkill'])->name('profile.skills.store');
    Route::put('/profile/skills/{skill}', [App\Http\Controllers\ProfileController::class, 'updateSkill'])->name('profile.skills.update');
    Route::delete('/profile/skills/{skill}', [App\Http\Controllers\ProfileController::class, 'destroySkill'])->name('profile.skills.destroy');
});

// Username-based routes (must be last to avoid collision with static routes)
Route::get('/{username}', [UsersDashboardController::class, 'index'])
    ->where('username', '[a-zA-Z0-9_]+')
    ->middleware('auth')
    ->name('users.dashboard');

// Public profile routes (accessible by username)
Route::get('/{username}/profile', [App\Http\Controllers\ProfileController::class, 'show'])
    ->where('username', '[a-zA-Z0-9_]+')
    ->name('profile.show');


