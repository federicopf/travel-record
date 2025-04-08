<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SocialController extends Controller
{
    
    public function publicProfile(string $username)
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isPrivate = $user->private_profile;

        // Verifica se l'utente loggato segue quello che si sta visualizzando
        $isFollowing = false;
        if ($authUser && !$isOwnProfile) {
            $isFollowing = \App\Models\Follow::where('follower_id', $authUser->id)
                ->where('followed_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
        }

        $canViewPrivate = $isOwnProfile || $isFollowing;

        if ($isPrivate && !$canViewPrivate) {
            return Inertia::render('Profile/Public/Index', [
                'user' => [
                    'name' => $user->name,
                    'username' => $user->username,
                    'private' => true,
                    'preview' => false,
                ],
                'places' => [],
                'trips' => []
            ]);
        }

        return Inertia::render('Profile/Public/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'private' => false,
                'preview' => $isOwnProfile,
            ],
            'places' => $this->populateMapData($user->id),
            'trips' => $this->populateTripData($user->id)
        ]);
    }


    private function populateMapData(int $userId): array
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
                        'images' => $place->photos()
                            ->where(function ($query) {
                                $query->where('path', 'LIKE', '%.png')
                                    ->orWhere('path', 'LIKE', '%.jpg')
                                    ->orWhere('path', 'LIKE', '%.jpeg');
                            })
                            ->take(2)
                            ->pluck('path')
                            ->map(fn ($path) => "/storage/{$path}")
                            ->toArray(),
                    ];
                });
            })->values()->toArray(); // importante convertire a array puro
    }

    private function populateTripData(int $userId): array
    {
        return Trip::with('places.photos', 'places.hashtags')
            ->where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($trip) {
                // Raccolta degli hashtag unici associati ai luoghi del viaggio
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
                    'hashtags' => $hashtags
                ];
            })
            ->toArray();
    }
}
