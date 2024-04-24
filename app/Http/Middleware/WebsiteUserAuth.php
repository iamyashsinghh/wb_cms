<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebsiteUserAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {
        $token = $request->header('bearer');
        if ($token == null) {
            return response()->json(['success' => false, 'message' => 'Unauthorized request.']);
        }
        $vendor_user = User::where('remember_token', $token)->first();
        if (!$vendor_user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized request.']);
        }
        return $next($request);
    }
}
