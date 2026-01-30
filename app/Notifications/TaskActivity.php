<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TaskActivity extends Notification
{
    use Queueable;

    public function __construct(public $task, public string $action, public array $meta = []) {}

    public function via($notifiable)
    {
        return ['mail', 'database', \App\Notifications\Channels\WhatsAppChannel::class];
    }

    public function toMail($notifiable)
    {
        $subject = match ($this->action) {
            'created' => 'Task Created',
            'assigned' => 'Task Assigned to you',
            'reassigned' => 'Task Reassigned',
            'unassigned' => 'Task Unassigned',
            'status_changed' => 'Task Status Updated',
            'updated' => 'Task Updated',
            'deleted' => 'Task Deleted',
            default => 'Task Update',
        };

        $line = match ($this->action) {
            'created' => 'A new task has been created.',
            'assigned' => 'A task has been assigned to you.',
            'reassigned' => 'A task has been reassigned.',
            'unassigned' => 'You have been unassigned from a task.',
            'status_changed' => 'The status of a task has changed.',
            'updated' => 'A task assigned to you has been updated.',
            'deleted' => 'A task assigned to you has been deleted.',
            default => 'A task activity occurred.',
        };

        $taskTitle = $this->task->title ?? ($this->task['title'] ?? '');
        $mail = (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->line('Task: ' . $taskTitle);

        if (isset($this->meta['old_status'])) {
            $mail->line('Old status: '.$this->meta['old_status']);
        }
        if (isset($this->meta['new_status'])) {
            $mail->line('New status: '.$this->meta['new_status']);
        }

        $mail->action('View Task', url('/tasks'));

        return $mail;
    }

    /**
     * Prepare WhatsApp message payload
     */
    public function toWhatsApp($notifiable)
    {
        $taskTitle = $this->task->title ?? ($this->task['title'] ?? '');

        $message = match ($this->action) {
            'created' => "New task created: {$taskTitle}",
            'assigned' => "A task has been assigned to you: {$taskTitle}",
            'reassigned' => "A task has been reassigned to you: {$taskTitle}",
            'unassigned' => "You have been unassigned from task: {$taskTitle}",
            'status_changed' => "Task status changed: {$taskTitle} ({$this->meta['old_status'] ?? 'N/A'} -> {$this->meta['new_status'] ?? 'N/A'})",
            'updated' => "Task updated: {$taskTitle}",
            'deleted' => "Task deleted: {$taskTitle}",
            default => "Task update: {$taskTitle}",
        };

        return [
            'phone' => $notifiable->phone ?? null,
            'message' => $message
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'action' => $this->action,
            'task_id' => $this->task->id ?? null,
            'title' => $this->task->title ?? null,
            'meta' => $this->meta,
        ];
    }
}
