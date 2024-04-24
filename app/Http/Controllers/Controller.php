<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs,  ValidatesRequests;
    protected function interakt_wa_msg_send(int $phone_no, string $name, string $message, string $template_type) {
        // var_dump($phone_no);
        // die;
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://api.interakt.ai/v1/public/message/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    "countryCode" => "+91",
                    "phoneNumber" => $phone_no,
                    "callbackData" => "Send successfully.",
                    "type" => "Template",
                    "template" => [
                        "name" => $template_type,
                        "languageCode" => "en",
                        "headerValues" => [$name],
                        "bodyValues" => [$message],
                    ],
                ]),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic " . env('INTERAKT_KEY'),
                    'Content-Type: application/json',
                    'Cookie: ApplicationGatewayAffinity=a8f6ae06c0b3046487ae2c0ab287e175; ApplicationGatewayAffinityCORS=a8f6ae06c0b3046487ae2c0ab287e175'
                ),
            )
        );
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }
}
