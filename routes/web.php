<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BackupController;
use Illuminate\Support\Facades\DB;

// ڕاوتی سەرەکی: ئەگەر چووبێتە ژوورەوە دەچێتە Dashboard، ئەگەر نا دەچێتە Login
Route::get('/', function () {
    return redirect()->route('login');
});

// ڕاوتی تایبەت بۆ پشکنینی تەندروستی دەیتابەیس و بەستەرەکان
Route::get('/check-db', function () {
    return response()->json([
        'connection' => config('database.default'),
        'database'   => DB::connection()->getDatabaseName(),
    ]);
});

Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/driver', [MovementController::class, 'mine'])->name('driver.home');
    Route::get('/driver/departure', [MovementController::class, 'create'])->name('driver.departure');
    Route::post('/driver/departure', [MovementController::class, 'store'])->name('driver.departure.store');
    Route::get('/driver/active', [MovementController::class, 'active'])->name('driver.active');
    Route::post('/driver/return/{movement}', [MovementController::class, 'returnVehicle'])->name('driver.return');
    Route::get('/driver/history', [MovementController::class, 'mine'])->name('driver.history');

    Route::middleware('admin')->group(function () {
        Route::resource('drivers', DriverController::class)->except(['show']);
        Route::resource('vehicles', VehicleController::class)->except(['show']);
        Route::get('/movements', [MovementController::class, 'adminIndex'])->name('movements.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/csv', [ReportController::class, 'csv'])->name('reports.csv');
    });
});