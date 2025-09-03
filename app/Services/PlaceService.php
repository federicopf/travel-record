<?php

namespace App\Services;

use App\Models\Place;
use App\Models\Photo;
use App\Models\Hashtag;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlaceService
{
    public function getPlaceWithDetails(Trip $trip, Place $place): array
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);

        $place->load(['photos', 'hashtags']);
        
        return $this->formatPlaceForShow($place);
    }

    public function updatePlace(Trip $trip, Place $place, array $data): Place
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);

        $place->update([
            'name' => $data['name'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
        ]);

        return $place;
    }

    public function deletePlace(Trip $trip, Place $place): void
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);

        $path = "uploads/trips/{$trip->id}/{$place->id}";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }

        $place->delete();
    }

    public function addPlace(Trip $trip, array $data): Place
    {
        $this->authorizeTrip($trip->id);

        return $trip->places()->create([
            'name' => $data['name'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
        ]);
    }

    public function uploadPhotos(Trip $trip, Place $place, array $files): array
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);

        $uploadedPaths = [];

        foreach ($files as $file) {
            $path = "uploads/trips/{$trip->id}/{$place->id}";

            if ($this->isImageFile($file->getClientOriginalExtension())) {
                $destinationPath = storage_path("app/public/{$path}/" . time() . '-' . $file->getClientOriginalName());
                $this->compressImage($file->getPathname(), $destinationPath);
                $finalPath = str_replace(storage_path("app/public/"), '', $destinationPath);
            } else {
                $finalPath = $file->store($path, 'public');
            }

            $photo = Photo::create([
                'place_id' => $place->id,
                'path' => $finalPath
            ]);

            $uploadedPaths[] = $photo->path;
        }

        return $uploadedPaths;
    }

    public function deletePhoto(Trip $trip, Place $place, Photo $photo): void
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);
        $this->validatePhotoBelongsToPlace($photo, $place);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();
    }

    public function setFavoritePhoto(Trip $trip, Photo $photo): void
    {
        $this->authorizeTrip($trip->id);
        $this->validatePhotoBelongsToTrip($photo, $trip);

        $trip->update(['image' => $photo->path]);
    }

    public function updateHashtags(Trip $trip, Place $place, array $hashtagIds): void
    {
        $this->authorizeTrip($trip->id);
        $this->validatePlaceBelongsToTrip($place, $trip);

        $place->hashtags()->sync($hashtagIds);
    }

    public function getAllHashtags(): \Illuminate\Database\Eloquent\Collection
    {
        return Hashtag::all();
    }

    private function authorizeTrip(int $tripId): bool
    {
        $trip = Trip::find($tripId);

        if (!$trip || $trip->user_id !== Auth::id()) {
            return false;
        }

        return true;
    }

    private function validatePlaceBelongsToTrip(Place $place, Trip $trip): void
    {
        if ($place->trip_id !== $trip->id) {
            abort(403, 'Il posto non appartiene a questo viaggio');
        }
    }

    private function validatePhotoBelongsToPlace(Photo $photo, Place $place): void
    {
        if ($photo->place_id !== $place->id) {
            abort(403, 'Questa foto non appartiene al luogo selezionato');
        }
    }

    private function validatePhotoBelongsToTrip(Photo $photo, Trip $trip): void
    {
        if ($photo->place->trip_id !== $trip->id) {
            abort(403, 'Questa foto non appartiene al viaggio');
        }
    }

    private function formatPlaceForShow(Place $place): array
    {
        $place->photos->transform(function ($photo) {
            $photo->path = asset('storage/' . ltrim($photo->path, '/'));
            return $photo;
        });

        return [
            'place' => $place,
            'availableHashtags' => $this->getAllHashtags(),
        ];
    }

    private function isImageFile(string $extension): bool
    {
        return in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'webp']);
    }

    private function compressImage(string $sourcePath, string $destinationPath, int $quality = 75, int $maxWidth = 1920): bool
    {
        $directory = dirname($destinationPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        $image = $this->createImageFromFile($sourcePath, $mime);
        if (!$image) {
            return false;
        }

        $image = $this->fixImageOrientation($image, $sourcePath, $mime);
        $image = $this->resizeImageIfNeeded($image, $maxWidth);

        $this->saveImage($image, $destinationPath, $mime, $quality);
        imagedestroy($image);

        return true;
    }

    private function createImageFromFile(string $path, string $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    private function fixImageOrientation($image, string $path, string $mime)
    {
        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        break;
                    case 6:
                        $image = imagerotate($image, -90, 0);
                        break;
                    case 8:
                        $image = imagerotate($image, 90, 0);
                        break;
                }
            }
        }
        return $image;
    }

    private function resizeImageIfNeeded($image, int $maxWidth)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = intval(($height / $width) * $newWidth);
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            return $resizedImage;
        }

        return $image;
    }

    private function saveImage($image, string $path, string $mime, int $quality): void
    {
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($image, $path, $quality);
                break;
            case 'image/png':
                imagepng($image, $path, intval($quality / 10));
                break;
            case 'image/webp':
                imagewebp($image, $path, $quality);
                break;
        }
    }
}
