<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FestivalGreeting;

class SendFestivalGreetings extends Command
{
    protected $signature = 'app:send-festival-greetings {--dry-run}';
    protected $description = 'Send festival greetings (email + WhatsApp) based on config/festivals.php';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $festivals = config('festivals', []);
        $today = Carbon::today();

        foreach ($festivals as $key => $fest) {
            $sendBefore = intval($fest['send_before_days'] ?? 0);
            $occurrences = [];

            // explicit date
            if (!empty($fest['date'])) {
                try { $occurrences[] = Carbon::parse($fest['date']); } catch (\Throwable $e) {}
            }

            if (!empty($fest['dates']) && is_array($fest['dates'])) {
                foreach ($fest['dates'] as $d) {
                    try { $occurrences[] = Carbon::parse($d); } catch (\Throwable $e) {}
                }
            }

            if (!empty($fest['month_day'])) {
                // check current year and next year (cover year wrap sends)
                [$m,$d] = explode('-', $fest['month_day']);
                try {
                    $occurrences[] = Carbon::create($today->year, $m, $d);
                    $occurrences[] = Carbon::create($today->year + 1, $m, $d);
                } catch (\Throwable $e) {}
            }

            foreach ($occurrences as $occ) {
                // check range: today is occ - sendBefore
                $sendDate = $occ->copy()->subDays($sendBefore);
                if ($sendDate->isSameDay($today)) {
                    // prepare recipients: all customers with phone or email
                    $customers = Customer::where(function($q){
                        $q->whereNotNull('email')->where('email', '<>', '')->orWhere(function($q2){
                            $q2->whereNotNull('phone')->where('phone','<>','');
                        });
                    })->get();

                    // filter out customers already sent today for this festival
                    $customersToNotify = $customers->filter(function($c) use ($key, $occ) {
                        return ! $c->notifications()
                            ->where('type', FestivalGreeting::class)
                            ->whereDate('created_at', Carbon::today())
                            ->where('data->festival_key', $key)
                            ->exists();
                    });

                    $count = $customersToNotify->count();

                    if ($dryRun) {
                        $this->info("[DRY] Festival {$fest['name']} on {$occ->toDateString()} will be sent to {$count} recipients (key: {$key}).");
                        continue;
                    }

                    if ($count === 0) {
                        $this->info("No recipients to notify for {$fest['name']} ({$occ->toDateString()}).");
                        continue;
                    }

                    $this->info("Sending {$fest['name']} greetings for {$occ->toDateString()} to {$count} customers...");

                    // send in chunks to avoid memory issues
                    $customersToNotify->chunk(100)->each(function($chunk) use ($fest, $occ) {
                        Notification::send($chunk, new FestivalGreeting($fest, $occ->toDateString()));
                    });

                    $this->info("Done sending {$count} messages for {$fest['name']} ({$occ->toDateString()}).");
                }
            }
        }

        $this->info('Festival greeting run completed.');
        return 0;
    }
}
