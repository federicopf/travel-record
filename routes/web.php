<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ThemeController;

use App\Http\Middleware\AuthenticateUser;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware([AuthenticateUser::class])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('home');
    Route::get('/new-trip', [TripController::class, 'create'])->name('new-trip');
    Route::post('/new-trip', [TripController::class, 'store'])->name('new-trip.store');

    Route::get('/trip/{trip}', [TripController::class, 'show'])->name('trip.show');
    Route::get('/trip/{trip}/edit', [TripController::class, 'edit'])->name('trip.edit');
    Route::post('/trip/{trip}', [TripController::class, 'update'])->name('trip.update');
    Route::delete('/trip/{trip}', [TripController::class, 'destroy'])->name('trip.destroy');

    Route::get('/trip/{trip}/place/{place}', [PlaceController::class, 'show'])->name('trip.place.show');
    Route::post('/trip/{trip}/place/{place}/photo', [PlaceController::class, 'uploadPhoto'])->name('trip.place.photo.upload');
    Route::delete('/trip/{trip}/place/{place}/photo/{photo}', [PlaceController::class, 'deletePhoto'])->name('trip.place.photo.delete');
    Route::post('/trip/{trip}/photo/{photo}/set-favorite', [PlaceController::class, 'setFavoritePhoto'])->name('trip.photo.setFavorite');

    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.update');

    Route::post('/change-theme', [ThemeController::class, 'change'])->name('theme.change');
    Route::post('/change-map-pointer', [ThemeController::class, 'changeMapPointer'])->name('map-pointer.change');

    Route::get('/map', [MapController::class, 'index'])->name('map');
});
