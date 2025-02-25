<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        // Recuperiamo tutti i posti con le loro coordinate
        $places = Trip::with('places')->get()->flatMap(function ($trip) {
            return $trip->places->map(function ($place) use ($trip) {
                return [
                    'trip' => $trip->title,
                    'name' => $place->name,
                    'lat' => $place->lat,
                    'lng' => $place->lng,
                ];
            });
        });

        return Inertia::render('Map', [
            'places' => $places
        ]);
    }
}
