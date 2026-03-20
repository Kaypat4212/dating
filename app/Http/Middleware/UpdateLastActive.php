<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $cacheKey = "user_last_active_{$user->id}";

            // Only update DB once every 5 minutes per user (cache throttle)
            if (!Cache::has($cacheKey)) {
                $user->update(['last_active_at' => now()]);
                Cache::put($cacheKey, true, now()->addMinutes(5));
            }
        }

        return $next($request);
    }
}
