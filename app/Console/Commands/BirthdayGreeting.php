<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Customer;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BirthdayGreeting extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:birthday-greeting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            if ($customer->email) {
            Notification::route('mail', $customer->email)
                ->notify(new BirthdayGreeting());
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
