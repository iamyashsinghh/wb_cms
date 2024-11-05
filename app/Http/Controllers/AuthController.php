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
use App\Models\Device;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

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

        $agent = new Agent();
        $browser_name = $agent->browser();
        $browser_version = $agent->version($browser_name);
        $platform = $agent->platform();
        $client_ip = $request->getClientIp();

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'alert_type' => 'error',
                'message' => $validate->errors()->first()
            ], 400);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials.'], 400);
        } else if ($user->status == 0) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact your manager.'], 400);
        }

        $currentTime = date('H:i:s');
        if ($user->is_all_time_login === 0) {
            if ($user->login_start_time && $user->login_end_time) {
                if ($currentTime < $user->login_start_time || $currentTime > $user->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }

        $device_id = Cookie::get("device_id_cms_$user->phone");
        $datetime = date('Y-m-d H:i:s');
        $cookie_val = md5("$user->phone-$datetime");
        $can_user_login = 0;

        $verified_device = Device::where(['device_id' => $device_id])->where('member_id', $user->id)->first();

        $device = Device::where('member_id', $user->id)->first();

        if (!$device) {
            if ($user->can_add_device === 1) {
                $device = new Device();
                $device->member_id = $user->id;
                $device->device_name = "$browser_name Ver:$browser_version / Platform:$platform";
                $device->device_id = $cookie_val;
                if ($device->save()) {
                    $user->can_add_device = 0;
                    $user->save();
                    $can_user_login = 1;
                    Cookie::queue(Cookie::make("device_id_cms_$user->phone", $cookie_val, 60 * 24 * 30));
                }
            }
        } else {
            if ($user->can_add_device === 1) {
                $device = new Device();
                $device->member_id = $user->id;
                $device->device_name = "$browser_name Ver:$browser_version / Platform: $platform";
                $device->device_id = $cookie_val;
                $device->save();
                if ($device->save()) {
                    $user->can_add_device = 0;
                    $user->save();
                    $can_user_login = 1;
                    Cookie::queue(Cookie::make("device_id_cms_$user->phone", $cookie_val, 60 * 24 * 30));
                }
            }

            if ($verified_device) {
                $can_user_login = 1;
                $verified_device->device_id  = $cookie_val;
                $verified_device->save();
                Cookie::queue(Cookie::make("device_id_cms_$user->phone", $cookie_val, 60 * 24 * 30));
            }
        }

        if ($can_user_login === 1) {
            $otp = rand(100000, 999999);
            // $otp = 999999;

            $login_info = LoginInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp_code' => Hash::make($otp),
                    'request_otp_at' => Carbon::now(),
                    'ip_address' => $request->ip(),
                    'status' => 0,
                ]
            );
            if ($user->email) {
                Mail::to($user->email)->send(new OtpMail($otp, $user));
            }
            $this->sendWhatsAppMessage($user->phone, $user->name, $otp);

            return response()->json(['success' => true, 'alert_type' => 'success', 'message' => 'Verification code has been sent to your registered WhatsApp & Email.'], 200);
        } else {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Your device is not registed please ask admin for the registration'], 500);
        }
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

        if (!$user) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Invalid credentials.'], 400);
        } else if ($user->status == 0) {
            return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'Profile is inactive, kindly contact your manager.'], 400);
        }

        $currentTime = date('H:i:s');
        if ($user->is_all_time_login === 0) {
            if ($user->login_start_time && $user->login_end_time) {
                if ($currentTime < $user->login_start_time || $currentTime > $user->login_end_time) {
                    return response()->json(['success' => false, 'alert_type' => 'error', 'message' => 'You are not allowed to login at this time.'], 400);
                }
            }
        }

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
        $token = env("TATA_AUTH_KEY");
        Log::info("wa send");
        Log::info("91{$phone}");
        Log::info("$token");
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
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
        return $response;
    }
}
