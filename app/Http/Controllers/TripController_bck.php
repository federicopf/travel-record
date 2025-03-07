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
            'places' => 'required|array|min:1',
            'places.*.id' => 'nullable|integer|exists:places,id',
            'places.*.name' => 'required|string|max:255',
            'places.*.lat' => 'required|numeric',
            'places.*.lng' => 'required|numeric',
            'newPhotos.*.*' => 'file|mimes:jpeg,png,jpg,svg,webp,mp4,mov,avi|max:102400',
            'deletedPhotos' => 'array',
            'deletedPhotos.*' => 'integer|exists:photos,id',
            'favorite_photo' => 'nullable|string',
        ]);

        $trip->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        // 🔥 Eliminazione delle foto selezionate
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
                    $relativePath = "uploads/trips/{$trip->id}/{$placeId}/{$fileName}";
                    $absolutePath = storage_path("app/public/{$relativePath}");

                    // Se è un'immagine, comprimila e ridimensionala
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp'])) {
                        $this->compressImage($file->getPathname(), $absolutePath, 75, 1920);
                    } 
                    // Se è un video, salvalo senza modificarlo
                    else if (in_array($extension, ['mp4', 'mov', 'avi', 'svg'])) {
                        $file->storeAs("uploads/trips/{$trip->id}/{$placeId}", $fileName, 'public');
                    }

                    $place->photos()->create(['path' => $relativePath]);

                    // Se è l'immagine preferita, aggiorna il percorso
                    if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp']) && $validated['favorite_photo'] === $file->getClientOriginalName()) {
                        $favoriteImagePath = $relativePath;
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
            // Se nessuna immagine è preferita, usa la prima disponibile
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

    private function compressImage($sourcePath, $destinationPath, $quality = 75, $maxWidth = 1920)
    {
        // Crea la cartella di destinazione se non esiste
        $directory = dirname($destinationPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Ottieni il tipo di immagine
        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        // Crea l'immagine in base al tipo
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false; // Tipo di file non supportato
        }

        // Corregge l'orientamento dell'immagine se è un JPEG
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                switch ($orientation) {
                    case 3: // Ruotare di 180 gradi
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6: // Ruotare di 90 gradi in senso orario
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8: // Ruotare di 90 gradi in senso antiorario
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }
        }

        // Ridimensiona se è più grande della larghezza massima
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval(($height / $width) * $newWidth);
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $resizedImage;
        }

        // Salva l'immagine compressa
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($image, $destinationPath, $quality);
                break;
            case 'image/png':
                imagepng($image, $destinationPath, intval($quality / 10)); // PNG usa scala 0-9
                break;
            case 'image/webp':
                imagewebp($image, $destinationPath, $quality);
                break;
        }

        imagedestroy($image);
        return true;
    }


}
