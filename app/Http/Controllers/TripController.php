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
            'places.*.photos' => 'array|max:10', // ✅ Permettiamo fino a 10 file per posto
            'places.*.photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:51200', // ✅ Accetta immagini e video (50MB max per video)
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
                    $path = $photo->storeAs("uploads/trips/{$trip->id}/{$place->id}", $fileName, 'public');
    
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp']) && $favoritePhotoName === $photo->getClientOriginalName()) {
                        $favoriteImagePath = $path;
                    }
    
                    $place->photos()->create(['path' => $path]);
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
    
        return Redirect::route('home')->with('success', 'Viaggio aggiunto con successo!');
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
            'places' => 'required|array|min:1',
            'places.*.id' => 'nullable|integer|exists:places,id',
            'places.*.name' => 'required|string|max:255',
            'places.*.lat' => 'required|numeric',
            'places.*.lng' => 'required|numeric',
            'places.*.photos' => 'array|max:30', // ✅ Supporta fino a 30 file tra immagini e video
            'places.*.photos.*.id' => 'nullable|integer|exists:photos,id',
            'places.*.photos.*.path' => 'nullable|string',
            'newPhotos.*.*' => 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:51200', // ✅ Accetta immagini e video
            'deletedPhotos' => 'array',
            'deletedPhotos.*' => 'integer|exists:photos,id',
            'favorite_photo' => 'nullable|string',
        ]);

        $trip->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        if (!empty($validated['deletedPhotos'])) {
            $photosToDelete = Photo::whereIn('id', $validated['deletedPhotos'])->get();
            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }
        }

        $favoriteImagePath = null;

        foreach ($validated['places'] as $placeData) {
            $place = $trip->places()->updateOrCreate(
                ['id' => $placeData['id']],
                [
                    'name' => $placeData['name'],
                    'lat' => $placeData['lat'],
                    'lng' => $placeData['lng'],
                ]
            );

            $placeId = $place->id;

            if (!empty($validated['newPhotos'][$placeId])) {
                foreach ($validated['newPhotos'][$placeId] as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = time() . '-' . uniqid() . '.' . $extension;
                    $path = $file->storeAs("uploads/trips/{$trip->id}/{$placeId}", $fileName, 'public');

                    $photo = $place->photos()->create([
                        'path' => $path,
                    ]);

                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp']) && $validated['favorite_photo'] === $file->getClientOriginalName()) {
                        $favoriteImagePath = $path;
                    }
                }
            }
        }

        if (!$favoriteImagePath && !empty($validated['favorite_photo'])) {
            if (str_contains($validated['favorite_photo'], 'uploads/')) {
                $validated['favorite_photo'] = 'uploads/' . explode('uploads/', $validated['favorite_photo'])[1];
            }

            $existingPhoto = Photo::where('path', $validated['favorite_photo'])->first();

            if ($existingPhoto) {
                $favoriteImagePath = $existingPhoto->path;
            }
        }

        if ($favoriteImagePath) {
            $trip->update(['image' => $favoriteImagePath]);
        } else {
            // Se nessuna immagine è preferita, mettiamo la prima immagine disponibile
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

        return Redirect::route('trip.show', $trip)->with('success', 'Viaggio aggiornato con successo!');
    }


    public function destroy(Trip $trip)
    {
        // Controlla se l'utente ha il permesso di eliminare il viaggio
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home')->with('error', 'Non sei autorizzato a eliminare questo viaggio.');
        }

        // Elimina tutte le foto associate ai luoghi del viaggio
        foreach ($trip->places as $place) {
            foreach ($place->photos as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }
            $place->delete();
        }

        // Elimina l'immagine principale del viaggio (se presente)
        if ($trip->image) {
            Storage::disk('public')->delete($trip->image);
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
