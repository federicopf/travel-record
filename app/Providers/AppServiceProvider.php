<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\MapPointer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    
    public function boot()
    {
        Inertia::share([
            'auth' => function () {
                $user = Auth::user();
                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'type' => $user->type,
                        'color_scheme' => $user->theme ? $user->theme->color_scheme : 'blue', 
                        'map_pointer_url' => $user->mapPointer ? $user->mapPointer->url : null, 
                    ] : null
                ];
            },
            'mapPointers' => MapPointer::all(), 
        ]);
    }
}
