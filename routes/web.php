<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/masjid/logo', [SettingsController::class, 'logo'])->name('mosque.logo');
Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
Route::get('/laporan/ekspor/pdf', [ReportController::class, 'pdf'])->middleware('throttle:10,1')->name('reports.pdf');
Route::get('/laporan/ekspor/excel', [ReportController::class, 'excel'])->middleware('throttle:10,1')->name('reports.excel');

Route::get('/infaq', [DonationController::class, 'create'])->name('donations.create');
Route::post('/infaq', [DonationController::class, 'store'])->middleware('throttle:10,1')->name('donations.store');
Route::get('/infaq/konfirmasi/{paymentReference}', [DonationController::class, 'confirmation'])
    ->name('donations.confirmation');
Route::get('/infaq/kwitansi/{paymentReference}', [DonationController::class, 'receipt'])->middleware('throttle:10,1')
    ->name('donations.receipt');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegistration'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/infaq/riwayat', [DonationController::class, 'history'])->name('donations.history');
    Route::get('/infaq/{donation}/bukti', [DonationController::class, 'proof'])->name('donations.proof');
});

Route::middleware(['auth', 'role:admin,bendahara'])->group(function (): void {
    Route::resource('transaksi', TransactionController::class)
        ->except(['show'])
        ->parameters(['transaksi' => 'transaction'])
        ->names('transactions');
    Route::get('/transaksi/{transaction}/bukti', [TransactionController::class, 'proof'])
        ->name('transactions.proof');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan/masjid', [SettingsController::class, 'updateMosque'])->name('settings.mosque');
    Route::post('/kategori', [SettingsController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/kategori/{category}', [SettingsController::class, 'destroyCategory'])->name('categories.destroy');
    Route::post('/pengguna', [SettingsController::class, 'storeUser'])->name('users.store');
    Route::patch('/pengguna/{user}/role', [SettingsController::class, 'updateUserRole'])->name('users.role');
    Route::post('/infaq/{donation}/verifikasi', [DonationController::class, 'verify'])
        ->name('donations.verify');
    Route::post('/infaq/{donation}/tolak', [DonationController::class, 'reject'])
        ->name('donations.reject');
});
