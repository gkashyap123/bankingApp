<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssigned extends Notification
{
    use Queueable;

    public function __construct(public $task) {}

    public function via($notifiable)
    {
        return ['mail', 'database', \App\Notifications\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        $title = is_array($this->task) ? ($this->task['title'] ?? '') : ($this->task->title ?? '');

        return (new MailMessage)
            ->subject('New Task Assigned')
            ->line('A new task has been assigned to you.')
            ->line('Task: ' . $title)
            ->action('View Task', url('/tasks'));
    }

    public function toWhatsApp($notifiable)
    {
        $title = is_array($this->task) ? ($this->task['title'] ?? '') : ($this->task->title ?? '');
        $message = "A new task has been assigned to you: {$title}";

        return [
            'phone' => $notifiable->phone ?? null,
            'message' => $message
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'task_id' => is_array($this->task) ? ($this->task['id'] ?? null) : ($this->task->id ?? null),
            'title' => is_array($this->task) ? ($this->task['title'] ?? null) : ($this->task->title ?? null),
        ];
    }
}
