<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Middleware\IpAuthentication;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/', [TripController::class, 'index'])->name('home')->middleware(IpAuthentication::class);
Route::get('/new-trip', [TripController::class, 'create'])->name('new-trip')->middleware(IpAuthentication::class);
