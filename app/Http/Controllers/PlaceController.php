<?php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use App\Models\Trip;
use App\Models\Place;
use App\Models\Photo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

use Inertia\Inertia;

class PlaceController extends Controller
{
    public function show(Trip $trip, Place $place)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $place->load('photos');

        // Aggiungere il prefisso `/storage/` ai percorsi delle immagini
        $place->photos->transform(function ($photo) {
            $photo->path = asset('storage/' . ltrim($photo->path, '/'));
            return $photo;
        });

        $availableHashtags = Hashtag::all();

        return Inertia::render('Trip/Place/Show', [
            'trip' => $trip,
            'place' => $place,
            'availableHashtags' => $availableHashtags,
        ]);
    }

    public function destroy(Trip $trip, Place $place)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        if ($place->trip_id !== $trip->id) {
            return response()->json(['error' => 'Il posto non appartiene a questo viaggio'], 403);
        }

        $path = "uploads/trips/{$trip->id}/{$place->id}";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }

        $place->delete();

        return Redirect::route('trip.show',$trip->id)->with('success', 'Posto eliminato con successo.');
    }
    
    public function edit(Trip $trip, Place $place)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        return Inertia::render('Trip/Place/Edit', [
            'trip' => $trip,
            'place' => $place,
        ]);
    }
    
    public function update(Request $request, Trip $trip, Place $place)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        if ($place->trip_id !== $trip->id) {
            abort(403, 'Accesso non autorizzato.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $place->update($validated);

        return redirect()
            ->route('trip.place.show', ['trip' => $trip->id, 'place' => $place->id])
            ->with('success', 'Posizione aggiornata con successo!');
    }

    public function uploadPhoto(Request $request, Trip $trip, Place $place)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $request->validate([
            'files' => 'required|array', // Assicura che files sia un array
            'files.*' => 'mimes:jpeg,png,jpg,svg,webp,mp4,webm,ogg|max:102400' // Valida ogni file
        ]);
    
        $uploadedPaths = [];
    
        foreach ($request->file('files') as $file) {
            $path = "uploads/trips/{$trip->id}/{$place->id}";
    
            if (in_array($file->getClientOriginalExtension(), ['jpeg', 'jpg', 'png', 'webp'])) {
                // Percorso temporaneo per salvare l'immagine compressa
                $destinationPath = storage_path("app/public/{$path}/" . time() . '-' . $file->getClientOriginalName());
                $this->compressImage($file->getPathname(), $destinationPath);
                $finalPath = str_replace(storage_path("app/public/"), '', $destinationPath);
            } else {
                // Caricamento normale per i video
                $finalPath = $file->store($path, 'public');
            }
    
            // Salva il file nel database
            $photo = Photo::create([
                'place_id' => $place->id,
                'path' => $finalPath
            ]);
    
            $uploadedPaths[] = $photo->path;
        }
    
        return back()->with('success', 'File caricati con successo!');
    }

    public function deletePhoto(Request $request, Trip $trip, Place $place, Photo $photo)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        if ($photo->place_id !== $place->id) {
            return back()->withErrors('Questa foto non appartiene al luogo selezionato.');
        }

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto eliminata con successo!');
    }

    public function setFavoritePhoto(Request $request, Trip $trip, Photo $photo)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        if ($photo->place->trip_id !== $trip->id) {
            return back()->withErrors('Questa foto non appartiene al viaggio.');
        }

        $trip->update(['image' => $photo->path]);

        return back()->with('success', 'Immagine preferita impostata con successo!');
    }

    public function addPlace(Request $request, Trip $trip)
    {
        if (!$this->authorizeTrip($trip->id)) {
            return Redirect::route('home');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $place = new Place();
        $place->trip_id = $trip->id;
        $place->name = $request->name;
        $place->lat = $request->lat;
        $place->lng = $request->lng;
        $place->save();

        return redirect()->route('trip.show', $trip->id)->with('success', 'Luogo aggiunto con successo!');
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

    private function authorizeTrip(int $tripId)
    {
        $trip = Trip::find($tripId);

        if (!$trip || $trip->user_id !== Auth::id()) {
            return false;
        }

        return true;
    }
}
