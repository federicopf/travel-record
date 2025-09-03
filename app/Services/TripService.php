<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Place;
use App\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class TripService
{
    public function getUserTrips(int $userId): Collection
    {
        return Trip::with(['places.photos', 'places.hashtags'])
            ->where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($trip) {
                return $this->formatTripForList($trip);
            });
    }

    public function createTrip(array $data): Trip
    {
        $trip = Trip::create([
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'image' => null,
            'user_id' => Auth::id()
        ]);

        $this->createPlacesForTrip($trip, $data['places'], $data['favorite_photo'] ?? null);
        $this->setTripImage($trip);

        return $trip;
    }

    public function getTripWithDetails(Trip $trip): array
    {
        $this->authorizeTrip($trip->id);
        
        $trip->load(['places.hashtags', 'places.photos']);
        
        return $this->formatTripForShow($trip);
    }

    public function updateTrip(Trip $trip, array $data): Trip
    {
        $this->authorizeTrip($trip->id);
        
        $trip->update([
            'title' => $data['title'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ]);

        return $trip;
    }

    public function deleteTrip(Trip $trip): void
    {
        $this->authorizeTrip($trip->id);
        
        // Delete associated files
        $path = "uploads/trips/{$trip->id}";
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->deleteDirectory($path);
        }

        // Delete associated data
        foreach ($trip->places as $place) {
            foreach ($place->photos as $photo) {
                $photo->delete();
            }
            $place->delete();
        }

        $trip->delete();
    }

    public function authorizeTrip(int $tripId): bool
    {
        $trip = Trip::find($tripId);
        
        if (!$trip || $trip->user_id !== Auth::id()) {
            return false;
        }

        return true;
    }

    private function createPlacesForTrip(Trip $trip, array $places, ?string $favoritePhotoName): void
    {
        $favoriteImagePath = null;

        foreach ($places as $placeData) {
            $place = $trip->places()->create([
                'name' => $placeData['name'],
                'lat' => $placeData['lat'],
                'lng' => $placeData['lng'],
            ]);

            // Handle photo uploads if present
            if (!empty($placeData['photos'])) {
                foreach ($placeData['photos'] as $photo) {
                    $photoPath = $this->processPhotoUpload($photo, $trip->id, $place->id);
                    
                    if ($photoPath && $favoritePhotoName === $photo->getClientOriginalName()) {
                        $favoriteImagePath = $photoPath;
                    }
                }
            }
        }

        if ($favoriteImagePath) {
            $trip->update(['image' => $favoriteImagePath]);
        }
    }

    private function setTripImage(Trip $trip): void
    {
        if ($trip->image) {
            return;
        }

        $firstImage = optional($trip->places()
            ->with('photos')
            ->get()
            ->flatMap(fn ($place) => $place->photos)
            ->filter(fn ($photo) => $this->isImageFile($photo->path))
            ->first())->path;

        if ($firstImage) {
            $trip->update(['image' => $firstImage]);
        }
    }

    private function processPhotoUpload($photo, int $tripId, int $placeId): ?string
    {
        $extension = $photo->getClientOriginalExtension();
        $fileName = time() . '-' . uniqid() . '.' . $extension;
        $relativePath = "uploads/trips/{$tripId}/{$placeId}/{$fileName}";
        $absolutePath = storage_path("app/public/{$relativePath}");

        if ($this->isImageFile($extension)) {
            return $this->compressAndSaveImage($photo->getPathname(), $absolutePath, $relativePath);
        } else {
            $photo->storeAs("uploads/trips/{$tripId}/{$placeId}", $fileName, 'public');
            return $relativePath;
        }
    }

    private function compressAndSaveImage(string $sourcePath, string $destinationPath, string $relativePath): string
    {
        $directory = dirname($destinationPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $info = getimagesize($sourcePath);
        $mime = $info['mime'];

        $image = $this->createImageFromFile($sourcePath, $mime);
        if (!$image) {
            return '';
        }

        $image = $this->fixImageOrientation($image, $sourcePath, $mime);
        $image = $this->resizeImageIfNeeded($image, 1920);

        $this->saveImage($image, $destinationPath, $mime, 75);
        imagedestroy($image);

        return $relativePath;
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

    private function isImageFile(string $path): bool
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'webp']);
    }

    private function formatTripForList(Trip $trip): array
    {
        $hashtags = $trip->places
            ->flatMap->hashtags
            ->unique('id')
            ->values();

        return [
            'id' => $trip->id,
            'title' => $trip->title,
            'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
            'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),
            'image' => $trip->image ? "/storage/{$trip->image}" : null,
            'hashtags' => $hashtags,
        ];
    }

    private function formatTripForShow(Trip $trip): array
    {
        $trip->image = $trip->image ? "/storage/{$trip->image}" : null;
        $trip->start_date = Carbon::parse($trip->start_date)->format('d/m/Y');
        $trip->end_date = Carbon::parse($trip->end_date)->format('d/m/Y');

        $trip->places->transform(function ($place) {
            $filteredPhotos = collect($place->photos)->filter(function ($photo) {
                return $this->isImageFile($photo->path);
            })->values();

            $firstPhoto = $filteredPhotos->isNotEmpty() ? "/storage/" . $filteredPhotos->first()->path : null;
            $place->firstPhoto = $firstPhoto;

            return $place;
        });

        return $trip->toArray();
    }
}
