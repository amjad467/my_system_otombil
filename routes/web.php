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
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\DB;

// Root redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Database health check
Route::get('/check-db', function () {
    return response()->json([
        'connection' => config('database.default'),
        'database'   => DB::connection()->getDatabaseName(),
    ]);
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Authenticated routes (Drivers and Admins)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Global Search
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Driver Movement Actions
    Route::get('/driver', [MovementController::class, 'mine'])->name('driver.home');
    Route::get('/driver/departure', [MovementController::class, 'create'])->name('driver.departure');
    Route::post('/driver/departure', [MovementController::class, 'store'])->name('driver.departure.store');
    Route::get('/driver/active', [MovementController::class, 'active'])->name('driver.active');
    Route::post('/driver/return/{movement}', [MovementController::class, 'returnVehicle'])->name('driver.return');
    Route::get('/driver/history', [MovementController::class, 'mine'])->name('driver.history');

    // Admin Only routes
    Route::middleware('admin')->group(function () {
        Route::get('/movements', [MovementController::class, 'adminIndex'])->name('movements.index');
        Route::resource('drivers', DriverController::class)->except(['show']);
        Route::resource('vehicles', VehicleController::class)->except(['show']);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/csv', [ReportController::class, 'csv'])->name('reports.csv');

        // Backups
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{filename}/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});