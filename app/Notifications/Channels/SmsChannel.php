<?php

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        Http::post('https://api.msg91.com/api/sendhttp.php', [
            'authkey' => config('services.msg91.key'),
            'mobiles' => $notifiable->phone,
            'message' => $message,
            'sender' => 'FUNDMS',
            'route' => 4
        ]);
    }
}

?>