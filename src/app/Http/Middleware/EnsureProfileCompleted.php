<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $completed = $user && !is_null(optional($user->profile)->profile_completed_at);

        if ($user && !$completed && !$request->is('mypage/profile*')) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}
