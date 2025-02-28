<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;

use App\Models\Trip;

use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function index()
    {
        $places = Trip::with('places', 'places.photos')
            ->where('user_id', Auth::id())
            ->get()
            ->flatMap(function ($trip) {
                return $trip->places->map(function ($place) use ($trip) {
                    return [
                        'trip' => $trip->title,
                        'trip_id' => $trip->id, 
                        'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'), 
                        'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),    
                        'name' => $place->name,
                        'lat' => $place->lat,
                        'lng' => $place->lng,
                        'images' => $place->photos->take(3)->pluck('path')->map(fn($path) => "/storage/{$path}")->toArray(),
                    ];
                });
            });

        return Inertia::render('Map', [
            'places' => $places
        ]);
    }

}
