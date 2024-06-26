<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\LoginInfo;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function send_otp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'phone' => 'required|digits_between:10,15|exists:users,phone',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'alert_type' => 'error',
                'message' => $validate->errors()->first()
            ], 400);
        }

        $user = User::where('phone', $request->phone)->first();
        // $otp = 999999; // Hardcoded for testing, replace with rand(100000, 999999) in production
        $otp = rand(100000, 999999);

        $login_info = LoginInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp_code' => Hash::make($otp),
                'request_otp_at' => Carbon::now(),
                'ip_address' => $request->ip(),
                'status' => 0,
            ]
        );

        if($user->email){
            Mail::to($user->email)->send(new OtpMail($otp, $user));
        }

        $this->sendWhatsAppMessage($user->phone,$user->name,$otp);
        return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Verification code has been sent to your registered WhatsApp & Email.'], 200);
    }

    public function verify_otp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'verified_phone' => 'required|digits_between:10,15|exists:users,phone',
            'otp' => 'required|digits:6',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $user = User::where('phone', $request->verified_phone)->first();
        $login_info = LoginInfo::where('user_id', $user->id)->first();

        if (!$login_info || Carbon::parse($login_info->request_otp_at)->greaterThan(Carbon::now()->addMinutes(10))) {
            return redirect()->back()->withErrors(['otp' => 'OTP has expired.'])->withInput();
        }

        if (!Hash::check($request->otp, $login_info->otp_code)) {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.'])->withInput();
        }

        Auth::logoutOtherDevices($user->password);

        Auth::login($user);

        $login_info->update([
            'login_at' => Carbon::now(),
            'status' => 1,
            'otp_code' => null,
        ]);

        return redirect()->intended('/dashboard');
    }

    public function logout()
    {
        $user = Auth::user();
        $login_info = LoginInfo::where('user_id', $user->id)->first();
        $login_info->update([
            'logout_at' => Carbon::now(),
            'status' => 0,
        ]);

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    private function sendWhatsAppMessage($phone, $name, $otp)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $token = env("TATA_AUTH_KEY");
        $authToken = "Bearer $token";
        $response = Http::withHeaders([
            'Authorization' => $authToken,
            'Content-Type' => 'application/json',
        ])->post($url, [
                    "to" => "91{$phone}",
                    "type" => "template",
                    "template" => [
                        "name" => "login_otp_new",
                        "language" => [
                            "code" => "en"
                        ],
                        "components" => [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => "$name",
                                    ]
                                ]
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => "$otp",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);
                Log::info( $response);
                return $response;
    }
}
