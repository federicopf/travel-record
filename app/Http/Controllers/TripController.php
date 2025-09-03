<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TripController extends Controller
{
    public function index()
    {
        $tripService = new TripService();
        $trips = $tripService->getUserTrips(Auth::id());

        return Inertia::render('Home', [
            'trips' => $trips,
        ]);
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
            'places.*.photos' => 'array|max:30', 
            'places.*.photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:5120',
            'favorite_photo' => 'nullable|string',
        ]);

        $tripService = new TripService();
        $trip = $tripService->createTrip($validated);

        return redirect()->route('trip.show', ['trip' => $trip->id]);
    }
        
    public function show(Trip $trip)
    {
        $tripService = new TripService();
        
        if (!$tripService->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }
        
        $tripData = $tripService->getTripWithDetails($trip);

        return Inertia::render('Trip/Show', [
            'trip' => $tripData
        ]);
    }
    
    public function edit(Trip $trip)
    {
        $tripService = new TripService();
        
        if (!$tripService->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $trip->image = $trip->image ? asset("storage/{$trip->image}") : null;

        foreach ($trip->places as $place) {
            foreach ($place->photos as $photo) {
                $photo->path = asset("storage/{$photo->path}");
            }
        }

        return Inertia::render('Trip/Edit', [
            'trip' => $trip
        ]);
    }

    public function update(Request $request, Trip $trip)
    {
        $tripService = new TripService();
        
        if (!$tripService->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tripService->updateTrip($trip, $validated);

        return Redirect::route('trip.show', $trip)->with('success', 'Viaggio aggiornato con successo!');
    }

    public function destroy(Trip $trip)
    {
        $tripService = new TripService();
        
        if (!$tripService->authorizeTrip($trip->id)) {
            return Redirect::route('home')->with('error', 'Non sei autorizzato a eliminare questo viaggio.');
        }
        
        $tripService->deleteTrip($trip);

        return Redirect::route('home')->with('success', 'Viaggio eliminato con successo.');
    }

}
