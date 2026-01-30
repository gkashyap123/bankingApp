<?php
namespace App\Services;

use GuzzleHttp\Client;

class NotifierService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendSms($phone, $message)
    {
        return $this->client->post(env('SMS_API_URL'), [
            'headers' => [
                'Authorization' => env('SMS_API_KEY'),
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'phone' => $phone,
                'message' => $message
            ]
        ]);
    }

    
}
?>