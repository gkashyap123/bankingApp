<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Customer;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BirthdayGreeting;
use Illuminate\Notifications\Messages\MailMessage;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-birthday-greetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The WhatsApp service instance.
     *
     * @var \App\Services\WhatsAppService
     */
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        parent::__construct();

        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $customers = Customer::whereMonth('dob', now()->month)
        ->whereDay('dob', now()->day)
        ->get();

        foreach ($customers as $customer) {
            // call WhatsApp service
            if ($customer->email && $customer->phone) {
                Notification::route('mail', $customer->email)
                    ->notify(new BirthdayGreeting());

                try {
                    $this->whatsAppService->sendWhatsAppMessage(
                        $customer->phone,
                        "🎉 Happy Birthday from Fund Manager Team!"
                    );
                } catch (\Throwable $e) {
                    // Log the failure and continue with other customers
                    \Log::error('WhatsApp send failed: '.$e->getMessage());
                }
                
            }
        }
    }

    public function via($notifiable){
        return ['mail','database','sms'];
    }

    public function toMail($notifiable){
        return (new MailMessage)
        ->greeting('Dear '.$notifiable->name)
        ->line('Happy Birthday! We wish you...')
        ->action('View Account', url('/'));
    }

    public function toArray($notifiable){
        return ['message' => 'Happy Birthday'];
    }

}
