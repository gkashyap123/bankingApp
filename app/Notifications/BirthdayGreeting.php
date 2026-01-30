<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use App\Services\NotifierService;

class BirthdayGreeting extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Dear ' . ($notifiable->name ?? ''))
            ->line('Happy Birthday! We wish you a wonderful day.')
            ->action('View Account', url('/'));
    }

    /**
     * Get the array representation for the database channel.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Happy Birthday',
        ];
    }

    /**
     * Send an SMS for the "sms" channel.
     * The app's NotifierService is used when available; otherwise a simple string is returned.
     */
    public function toSms($notifiable)
    {
        $phone = $notifiable->phone ?? $notifiable->mobile ?? null;
        $message = 'Happy Birthday, ' . ($notifiable->name ?? '') . '! Wishing you a wonderful day.';

        if (! $phone) {
            Log::warning('BirthdayGreeting: no phone number found for notifiable', [
                'notifiable_id' => $notifiable->id ?? null,
            ]);
            return ['success' => false, 'reason' => 'no_phone'];
        }

        try {
            $notifier = app(NotifierService::class);
            $notifier->sendSms($phone, $message);

            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('BirthdayGreeting: SMS send failed - ' . $e->getMessage(), [
                'notifiable_id' => $notifiable->id ?? null,
                'phone' => $phone,
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
