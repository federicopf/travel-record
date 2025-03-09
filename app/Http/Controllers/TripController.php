<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

use App\Models\Photo;
use App\Models\Trip;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with(['places.photos'])
            ->where('user_id', Auth::id())
            ->orderBy('start_date', 'desc') 
            ->get();

        $trips = $trips->map(function ($trip) {
            return [
                'id' => $trip->id,
                'title' => $trip->title,
                'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),
                'image' => $trip->image ? "/storage/{$trip->image}" : null,
            ];
        });

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

        $trip = Trip::create([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'image' => null,
            'user_id' => Auth::id()
        ]);

        $favoritePhotoName = $validated['favorite_photo'] ?? null;
        $favoriteImagePath = null;

        foreach ($validated['places'] as $placeData) {
            $place = $trip->places()->create([
                'name' => $placeData['name'],
                'lat' => $placeData['lat'],
                'lng' => $placeData['lng'],
            ]);

            if (!empty($placeData['photos'])) {
                foreach ($placeData['photos'] as $photo) {
                    $extension = $photo->getClientOriginalExtension();
                    $fileName = time() . '-' . uniqid() . '.' . $extension;
                    $relativePath = "uploads/trips/{$trip->id}/{$place->id}/{$fileName}";
                    $absolutePath = storage_path("app/public/{$relativePath}");

                    // Se è un'immagine, comprimila
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp'])) {
                        $this->compressImage($photo->getPathname(), $absolutePath, 75, 1920);
                    } 
                    // Se è un video, salvalo normalmente
                    else if (in_array($extension, ['mp4', 'mov', 'avi'])) {
                        $photo->storeAs("uploads/trips/{$trip->id}/{$place->id}", $fileName, 'public');
                    }

                    // Seleziona l'immagine preferita se specificata
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp']) && $favoritePhotoName === $photo->getClientOriginalName()) {
                        $favoriteImagePath = $relativePath;
                    }

                    $place->photos()->create(['path' => $relativePath]);
                }
            }
        }

        if ($favoriteImagePath) {
            $trip->update(['image' => $favoriteImagePath]);
        } else {
            $firstImage = optional($trip->places()
                ->with('photos') 
                ->get()
                ->flatMap(fn ($place) => $place->photos)
                ->filter(fn ($photo) => in_array(pathinfo($photo->path, PATHINFO_EXTENSION), ['jpeg', 'jpg', 'png', 'webp']))
                ->first())->path;

            if ($firstImage) {
                $trip->update(['image' => $firstImage]);
            }
        }

        return redirect()->route('trip.show', ['trip' => $trip->id]);
    }
        
    public function show(Trip $trip)
    {
        $trip->image = $trip->image ? "/storage/{$trip->image}" : null;

        $trip->start_date = Carbon::parse($trip->start_date)->format('d/m/Y');
        $trip->end_date = Carbon::parse($trip->end_date)->format('d/m/Y');

        $trip->places->each(function ($place) {
            $place->photos->each(function ($photo) {
                $photo->path = "/storage/{$photo->path}";
            });
        });

        return Inertia::render('Trip/Show', [
            'trip' => $trip
        ]);
    }
    
    public function edit(Trip $trip)
    {
        if(!$this->authorizeTrip($trip->id)){
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
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $trip->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        return Redirect::route('trip.show', $trip)->with('success', 'Viaggio aggiornato con successo!');
    }


    public function destroy(Trip $trip)
    {
        // Controlla se l'utente ha il permesso di eliminare il viaggio
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home')->with('error', 'Non sei autorizzato a eliminare questo viaggio.');
        }
        
        $path = "uploads/trips/{$trip->id}";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }

        // Elimina tutte le foto associate ai luoghi del viaggio
        foreach ($trip->places as $place) {
            foreach ($place->photos as $photo) {
                $photo->delete();
            }
            $place->delete();
        }

        // Elimina il viaggio
        $trip->delete();

        return Redirect::route('home')->with('success', 'Viaggio eliminato con successo.');
    }

    private function authorizeTrip(int $tripId)
    {
        $trip = Trip::find($tripId);

        if (!$trip || $trip->user_id !== Auth::id()) {
            return false;
        }

        return true;
    }

}
