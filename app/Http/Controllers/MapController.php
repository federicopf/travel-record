<?php

namespace App\Http\Controllers;

use App\Models\Trip;

use Carbon\Carbon;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        $places = Trip::with('places', 'places.photos')->get()->flatMap(function ($trip) {
            return $trip->places->map(function ($place) use ($trip) {
                return [
                    'trip' => $trip->title,
                    'trip_id' => $trip->id, // Per il link alla pagina del viaggio
                    'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'), // Conversione sicura
                    'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),     // Conversione sicura
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
