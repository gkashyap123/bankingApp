<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    // public function index()
    // {
    //     return view('dashboard', [
    //         'tasks' => Task::whereDate('due_date', today())->get(),
    //         'birthdays' => Customer::whereMonth('dob', now()->month)
    //                                 ->whereDay('dob', now()->day)->get(),
    //     ]);
    // }    

    public function index()
    {
        $taskCount = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();

        $todayBirthdays = Customer::whereMonth('dob', Carbon::now()->month)
            ->whereDay('dob', Carbon::now()->day)
            ->count();

        // Upcoming events in next 7 days
        $start = Carbon::today();
        $end = Carbon::today()->addDays(7);
        $upcomingEvents = \App\Models\Event::with('customer')
            ->whereBetween('event_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $upcomingEventsCount = \App\Models\Event::whereBetween('event_date', [$start->toDateString(), $end->toDateString()])->count();

        // Upcoming anniversaries in next 7 days (month-day comparison)
        $start_md = $start->format('m-d');
        $end_md = $end->format('m-d');

        $anniversariesQuery = \App\Models\Customer::whereNotNull('anniversary')
            ->where(function($q) use ($start_md, $end_md) {
                if ($start_md <= $end_md) {
                    $q->whereRaw("DATE_FORMAT(anniversary, '%m-%d') BETWEEN ? AND ?", [$start_md, $end_md]);
                } else {
                    // Year wrap-around (e.g., Dec -> Jan)
                    $q->whereRaw("DATE_FORMAT(anniversary, '%m-%d') >= ?", [$start_md])
                      ->orWhereRaw("DATE_FORMAT(anniversary, '%m-%d') <= ?", [$end_md]);
                }
            });

        $upcomingAnniversaries = $anniversariesQuery->limit(5)->get();
        $upcomingAnniversariesCount = $anniversariesQuery->count();

        return view('dashboard', compact(
            'taskCount',
            'completedTasks',
            'pendingTasks',
            'todayBirthdays',
            'upcomingEvents',
            'upcomingEventsCount',
            'upcomingAnniversaries',
            'upcomingAnniversariesCount'
        ));
    }

}
