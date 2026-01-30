<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use App\Models\Customer;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BirthdayGreeting;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\AnniversaryGreeting;

class SendGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-greetings';

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
                        "🎉 Wishing you a wonderful birthday and a year filled with prosperity and success. We value your partnership! "
                    );
                } catch (\Throwable $e) {
                    // Log the failure and continue with other customers
                    \Log::error('WhatsApp send failed: '.$e->getMessage());
                }
                
            }
        }


        $anniversaries = Customer::whereMonth('anniversary', $today->month)
            ->whereDay('anniversary', $today->day)
            ->get();

        foreach ($anniversaries as $customer) {
            
             if ($customer->email && $customer->phone) {
                Notification::route('mail', $customer->email)
                    ->notify(new AnniversaryGreeting());

                try {
                    $this->whatsAppService->sendWhatsAppMessage(
                        $customer->phone,
                        "🎉 Happy anniversary to a wonderful couple! We celebrate your milestone and appreciate the values of partnership and enduring commitment you bring to our team every day. Congratulations on this special day! "
                    );
                } catch (\Throwable $e) {
                    // Log the failure and continue with other customers
                    \Log::error('WhatsApp send failed: '.$e->getMessage());
                }
                
            }
            logger("Anniversary message sent to: {$customer->phone}");

        }

        $this->info('Greetings processed successfully');
    }

}
