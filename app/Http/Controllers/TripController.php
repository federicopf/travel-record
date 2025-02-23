<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use Inertia\Inertia;

use App\Models\Trip;

class TripController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }

    public function create()
    {
        return Inertia::render('NewTrip');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'places' => 'required|array|min:1',
            'places.*.name' => 'required|string|max:255',
            'places.*.lat' => 'required|numeric',
            'places.*.lng' => 'required|numeric',
            'places.*.photos' => 'array|max:10',
            'places.*.photos.*' => 'nullable|file|image|max:2048', // Max 2MB
        ]);
    
        // Salva il viaggio
        $trip = Trip::create([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);
    
        // Salva i luoghi e le foto
        foreach ($validated['places'] as $placeData) {
            $place = $trip->places()->create([
                'name' => $placeData['name'],
                'lat' => $placeData['lat'],
                'lng' => $placeData['lng'],
            ]);
    
            if (!empty($placeData['photos'])) {
                foreach ($placeData['photos'] as $index => $photo) {
                    // Salviamo le foto in `uploads/trips/{trip_id}/{place_id}/fotoX.jpg`
                    $path = $photo->store("uploads/trips/{$trip->id}/{$place->id}");
                    $place->photos()->create(['path' => $path]);
                }
            }
        }
    
        return Inertia::render('Home', [
            'flash' => ['success' => 'Viaggio salvato con successo!']
        ]);
    }
    
}
