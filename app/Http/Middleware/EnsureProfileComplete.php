<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->profile_complete) {
            // Allow access to onboarding routes and auth routes
            $allowedRoutes = [
                'setup.*',
                'photos.*',
                'logout',
                'verification.*',
            ];

            foreach ($allowedRoutes as $pattern) {
                if ($request->routeIs($pattern)) {
                    return $next($request);
                }
            }

            return redirect()->route('setup.step', ['step' => max(1, $user->onboarding_step + 1)]);
        }

        return $next($request);
    }
}
