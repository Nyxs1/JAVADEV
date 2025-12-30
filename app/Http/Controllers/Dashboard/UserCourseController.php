<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UserCourse;
use App\Support\Traits\JsonResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCourseController extends Controller
{
    use JsonResponses;

    /**
     * Display user's course enrollments in dashboard.
     */
    public function index()
    {
        $courses = Auth::user()->userCourses()->with('evidences')->get();

        return view('pages.dashboard.courses.index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Publish a course to public profile.
     */
    public function publish(UserCourse $userCourse)
    {
        // Authorization check
        if ($userCourse->user_id !== Auth::id()) {
            abort(403);
        }

        $userCourse->publish();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Course dipublikasikan. Sekarang terlihat di profil publikmu.', [
                'is_published' => true,
            ]);
        }

        return redirect()->route('dashboard.courses.index')
            ->with('success', 'Course dipublikasikan.');
    }

    /**
     * Unpublish a course (hide from public profile).
     */
    public function unpublish(UserCourse $userCourse)
    {
        // Authorization check
        if ($userCourse->user_id !== Auth::id()) {
            abort(403);
        }

        $userCourse->unpublish();

        if (request()->wantsJson()) {
            return $this->jsonSuccess('Course disembunyikan dari profil publik.', [
                'is_published' => false,
            ]);
        }

        return redirect()->route('dashboard.courses.index')
            ->with('success', 'Course disembunyikan.');
    }

    /**
     * Create a demo course (for testing purposes).
     * In production, courses would be enrolled via course catalog.
     */
    public function storeDemoEnrollment(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'progress_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $progress = $validated['progress_percent'] ?? 0;
        $status = $progress >= 100 ? 'completed' : 'in_progress';

        $course = Auth::user()->userCourses()->create([
            'course_id' => 'demo_' . uniqid(),
            'course_name' => $validated['course_name'],
            'progress_percent' => $progress,
            'status' => $status,
            'is_published' => false,
            'completed_at' => $status === 'completed' ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return $this->jsonSuccess('Course enrollment ditambahkan.', [
                'course' => $course,
            ]);
        }

        return redirect()->route('dashboard.courses.index')
            ->with('success', 'Course enrollment ditambahkan.');
    }
}
