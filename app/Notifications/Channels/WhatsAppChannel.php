<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\WhatsAppService;

class WhatsAppChannel
{
    protected WhatsAppService $whatsApp;

    public function __construct(WhatsAppService $whatsApp)
    {
        $this->whatsApp = $whatsApp;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $payload = $notification->toWhatsApp($notifiable);

        if (is_array($payload)) {
            $phone = $payload['phone'] ?? ($notifiable->phone ?? null);
            $message = $payload['message'] ?? ($payload['text'] ?? null);
        } else {
            $phone = $notifiable->phone ?? null;
            $message = $payload;
        }

        if (! $phone || ! $message) {
            return;
        }

        try {
            $this->whatsApp->sendWhatsAppMessage($phone, $message);
        } catch (\Throwable $e) {
            \Log::error('WhatsAppChannel send failed: '.$e->getMessage());
        }
    }
}
