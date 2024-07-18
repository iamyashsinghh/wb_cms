<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLoginTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

        $user = Auth::user();
        $currentTime = date('H:i:s');

        if($user->is_all_time_login === 0){
            if ($user->login_start_time && $user->login_end_time) {
                if ($currentTime < $user->login_start_time || $currentTime > $user->login_end_time) {
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->route('login')->with('status', ['success' => false, 'alert_type' => 'error', 'message' => 'Your session has expired due to login time restrictions.']);
                }
            }
        }
    }


        return $next($request);
    }
}
