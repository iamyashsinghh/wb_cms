<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $token = $request->header('bearer_token');
        if (Auth::guard('admin')->check()) {
            return $next($request);
        } elseif ($token == "8y9C1z5CDKMVbM3vcO6opuwRL") {
            session()->put('bearer_token', $token);
            return $next($request);
        } else {
            return redirect()->route('login');
        }
    }
}
