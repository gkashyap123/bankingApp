<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FestivalGreeting extends Notification
{
    use Queueable;

    public function __construct(public array $festival, public string $occursAt)
    {
        // $festival is the config entry; $occursAt is yyyy-mm-dd string for this occurrence
    }

    public function via($notifiable)
    {
        return ['mail', 'database', \App\Notifications\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        $message = $this->formatMessage($notifiable);

        return (new MailMessage)
            ->subject($this->festival['name'] . ' Greetings')
            ->line($message)
            ->line('Date: ' . $this->occursAt)
            ->action('Visit Fund Manager', url('/'));
    }

    public function toArray($notifiable)
    {
        return [
            'festival_key' => array_search($this->festival, config('festivals')) ?: null,
            'festival_name' => $this->festival['name'] ?? null,
            'occurs_at' => $this->occursAt,
            'message' => $this->formatMessage($notifiable),
        ];
    }

    public function toWhatsApp($notifiable)
    {
        return [
            'phone' => $notifiable->phone ?? null,
            'message' => $this->formatMessage($notifiable) . " (" . ($this->festival['name'] ?? '') . ")",
        ];
    }

    protected function formatMessage($notifiable)
    {
        $msg = $this->festival['message'] ?? ($this->festival['name'] . ' Greetings');
        $name = $notifiable->name ?? '';
        return str_replace('{name}', $name, $msg);
    }
}
