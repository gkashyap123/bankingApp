<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class WhatsAppService
{
    public function send($mobile, $message)
    {
        $response = Http::withHeaders([
            'authkey' => config('services.msg91.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/', [
            'integrated_number' => config('services.msg91.whatsapp_number'),
            'content_type' => 'template',
            'payload' => [
                'to' => $mobile,
                'type' => 'template',
                'template' => [
                    'name' => 'greeting_template',
                    'language' => ['code' => 'en'],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $message]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return $response->json();
    }


    public function sendWhatsAppMessage($phone, $message){
        $twilio = new Client(
                env('TWILIO_SID'),
                env('TWILIO_AUTH_TOKEN')
            );
         $twilio->messages->create(
                "whatsapp:{$phone}",
                [
                    "from" => 'whatsapp:+14155238886', //env('TWILIO_WHATSAPP_FROM'),//'whatsapp:+14155238886',//env('TWILIO_WHATSAPP_FROM'),
                    "body" => $message //"🎉 Happy Birthday from Fund Manager Team!"
                ]
            );
    }


}
