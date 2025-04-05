<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ThemeController;

use App\Http\Middleware\AuthenticateUser;
use App\Http\Middleware\EnsureUserIsSelf;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware([AuthenticateUser::class])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('home');
    Route::get('/new-trip', [TripController::class, 'create'])->name('new-trip');
    Route::post('/new-trip', [TripController::class, 'store'])->name('new-trip.store');

    Route::prefix('trip/{trip}')->group(function () {
        Route::get('/', [TripController::class, 'show'])->name('trip.show');
        Route::get('/edit', [TripController::class, 'edit'])->name('trip.edit');
        Route::post('/', [TripController::class, 'update'])->name('trip.update');
        Route::delete('/', [TripController::class, 'destroy'])->name('trip.destroy');
    
        Route::prefix('place/')->group(function () {
            Route::prefix('{place}')->group(function () {
                Route::get('/', [PlaceController::class, 'show'])->name('trip.place.show');
                Route::delete('/', [PlaceController::class, 'destroy'])->name('trip.place.destroy');
                Route::get('/edit', [PlaceController::class, 'edit'])->name('trip.place.edit');
                Route::put('/', [PlaceController::class, 'update'])->name('trip.place.update');

                Route::post('/photo', [PlaceController::class, 'uploadPhoto'])->name('trip.place.photo.upload');
                Route::delete('/photo/{photo}', [PlaceController::class, 'deletePhoto'])->name('trip.place.photo.delete');
                
                Route::post('/hashtags', [PlaceController::class, 'updateHashtags'])->name('trip.place.hashtags.update');
            });
        
            Route::post('place/{photo}/set-favorite', [PlaceController::class, 'setFavoritePhoto'])->name('trip.place.setFavorite');
            Route::post('place/add', [PlaceController::class, 'addPlace'])->name('trip.place.addPlace');
        });
    });    

    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.update');

    Route::post('/change-theme', [ThemeController::class, 'change'])->name('theme.change');
    Route::post('/change-map-pointer', [ThemeController::class, 'changeMapPointer'])->name('map-pointer.change');

    Route::get('/map', [MapController::class, 'index'])->name('map');

    Route::middleware([EnsureUserIsSelf::class])->group(function () {
        Route::prefix('/user/{user}/')->group(function () {
            Route::prefix('profile/')->group(function () {
                Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
            });
        });
    });
});
