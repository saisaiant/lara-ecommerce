<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/auth.php';