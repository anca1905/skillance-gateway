<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AutoreplyController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaGatewayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 1. ROUTE LOGIN & LOGOUT
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Redirect root ke devices
    Route::get('/', function () {
        return redirect()->route('devices.index');
    });

    // 1. KELOLA DEVICE (SAINGAN FONNTE)
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::put('/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');

    // 2. DOKUMENTASI API
    Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

    // 3. PENGATURAN AKUN
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');

    // ROUTE AUTO REPLY
    Route::get('/autoreply', [AutoreplyController::class, 'index'])->name('autoreply.index');
    Route::post('/autoreply', [AutoreplyController::class, 'store'])->name('autoreply.store');
    Route::delete('/autoreply/{id}', [AutoreplyController::class, 'destroy'])->name('autoreply.destroy');

    // ... route lain ...
    Route::get('/broadcast', [BroadcastController::class, 'index'])->name('broadcast.index');
    Route::post('/broadcast', [BroadcastController::class, 'send'])->name('broadcast.send');

    // Di dalam group auth
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
});
