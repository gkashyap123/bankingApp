<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EventController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//Route::get('/dashboard', [DashboardController::class,'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('tasks', TaskController::class);
Route::get('customers/import/template', [CustomerController::class, 'downloadTemplate'])->name('customers.import.template');
Route::get('customers/import', [CustomerController::class, 'importForm'])->name('customers.import');
Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import.post');


// Admin: delete all customers (use with caution)
Route::post('customers/delete-all', [CustomerController::class, 'destroyAll'])->name('customers.destroyAll');

Route::resource('customers', CustomerController::class);
Route::resource('events', EventController::class);


Route::resource('tasks', TaskController::class);



});


Route::get('/notifications', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return view('notifications.index', [
        'notifications' => auth()->user()->notifications
    ]);
})->name('notifications');


require __DIR__.'/auth.php';
