<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TripController;

use App\Http\Middleware\IpAuthentication;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([IpAuthentication::class])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('home');
    Route::get('/new-trip', [TripController::class, 'create'])->name('new-trip');
    Route::post('/new-trip', [TripController::class, 'store'])->name('new-trip.store');
    Route::get('/map', [MapController::class, 'index'])->name('map');
    
    Route::get('/trip/{trip}', [TripController::class, 'show'])->name('trip.show');
    Route::get('/trip/{trip}/edit', [TripController::class, 'edit'])->name('trip.edit');
    Route::put('/trip/{trip}', [TripController::class, 'update'])->name('trip.update');
});
