<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function login_process(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|max:20',
        ]);

        if ($validate->fails()) {
            session()->flash('status', ['success' => false, 'alert_type' => 'error', 'message' => "Invalid credentials.", 'error' => $validate->errors()]);
            return redirect()->back();
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        $authenticate = Auth::guard('admin')->attempt($credentials);
        if ($authenticate) {
            $user = Auth::guard('admin')->user();
            if ($user->id == 1 || $user->id == 5) {
                return redirect('/dashboard');
            } else {
                Auth::guard('admin')->logout();
                return redirect('/login')->withErrors(['email' => 'Invalid credentials']);
            }
        } else {
            // Authentication failed
            return redirect('/login')->withErrors(['email' => 'Invalid credentials']);
        }
    }

    public function logout()
    {
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
}
