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

        // ログイン済み & プロフィール未完了 & プロフィール画面以外 → /mypage/profile
        if ($user
            && is_null($user->profile_completed_at)
            && !$request->is('mypage/profile*')
        ) {
            return redirect('/mypage/profile');
        }

        return $next($request);
    }
}
