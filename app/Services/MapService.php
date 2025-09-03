<?php

namespace App\Services;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MapService
{
    public function getUserMapData(int $userId): \Illuminate\Support\Collection
    {
        return Trip::with('places', 'places.photos')
            ->where('user_id', $userId)
            ->get()
            ->flatMap(function ($trip) {
                return $trip->places->map(function ($place) use ($trip) {
                    return [
                        'trip' => $trip->title,
                        'trip_id' => $trip->id,
                        'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
                        'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),
                        'name' => $place->name,
                        'lat' => $place->lat,
                        'lng' => $place->lng,
                        'images' => $this->getPlaceImages($place),
                    ];
                });
            });
    }

    private function getPlaceImages($place): array
    {
        return $place->photos()
            ->where(function ($query) {
                $query->where('path', 'LIKE', '%.png')
                    ->orWhere('path', 'LIKE', '%.jpg')
                    ->orWhere('path', 'LIKE', '%.jpeg');
            })
            ->take(2)
            ->pluck('path')
            ->map(fn($path) => "/storage/{$path}")
            ->toArray();
    }
}
