<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMentor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow mentors and admins
        if (!$user->isMentor() && !$user->isAdmin()) {
            abort(403, 'Access denied. Mentor role required.');
        }

        return $next($request);
    }
}
