<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\whatsappMessages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappMsgController extends Controller
{
    public function whatsapp_msg_get($id)
    {
        $perPage = 5;
        $messages = whatsappMessages::where('msg_from', $id)
            ->orderBy('time', 'desc')
            ->paginate($perPage);
        foreach ($messages as $message) {
            if (!empty($message->doc)) {
                $mediaUrl = $this->get_whatsapp_doc($message->doc);
                $message->doc = $mediaUrl;
            }
        }
        return response()->json($messages);
    }

    public function whatsapp_msg_send(Request $request)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $url = "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/messages";
        $authKey = env('TATA_AUTH_KEY');
        $response = Http::withHeaders([
            'Authorization' => "Bearer $authKey",
            'Content-Type' => 'application/json'
        ])->post($url, [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => "91$request->recipient",
            "type" => "text",
            "text" => [
                "body" => "$request->message"
            ]
        ]);
        if ($response->successful()) {
            // Log::info($response);
            $currentTimestamp = Carbon::now();
            $newWaMsg = new whatsappMessages();
            $newWaMsg->msg_id = "$request->recipient";
            $newWaMsg->msg_from = "$request->recipient";
            $newWaMsg->time = $currentTimestamp;
            $newWaMsg->type = 'text';
            $newWaMsg->is_sent = "1";
            $newWaMsg->body = "$request->message";
            $newWaMsg->save();
            return response()->json(['message' => 'Message sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send message.'], $response->status());
        }
    }


    public function whatsapp_msg_get_new($id, Request $request)
    {
        $lastTimestamp = $request->input('lastTimestamp');

        $messages = whatsappMessages::where('msg_from', $id)
            ->where('time', '>', $lastTimestamp)
            ->orderBy('time', 'asc')
            ->get();

        foreach ($messages as $message) {
            if (!empty($message->doc)) {
                $mediaUrl = $this->get_whatsapp_doc($message->doc);
                $message->doc = $mediaUrl;
            }
        }

        return response()->json($messages);
    }

    public function get_whatsapp_doc($id)
    {
        if (env('TATA_WHATSAPP_MSG_STATUS') !== true) {
            return false;
        }
        $authKey = env('TATA_AUTH_KEY');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://wb.omni.tatatelebusiness.com/whatsapp-cloud/media/download/$id",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $authKey",
            ],
        ]);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (curl_errno($curl)) {
            // Log::error("Curl error: " . curl_error($curl));
            curl_close($curl);
            return null;
        }
        curl_close($curl);
        if ($httpcode >= 200 && $httpcode < 300) {
            $data = json_decode($response, true);
            // Log::info("Media URL for message: " . $response);
            return $data['url'] ?? null;
        } else {
            // Log::error("Request failed with HTTP status $httpcode and response: $response");
            return null;
        }
    }


}
