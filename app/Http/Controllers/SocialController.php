<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use App\Services\SocialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SocialController extends Controller
{
    
    public function publicProfile(string $username)
    {
        $socialService = new SocialService();
        $profileData = $socialService->getPublicProfile($username);

        return Inertia::render('Profile/Public/Index', $profileData);
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
            })->values()->toArray(); 
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

    public function publicTrip(string $username, Trip $trip)
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isFollowing = false;

        if ($authUser && !$isOwnProfile) {
            $isFollowing = \App\Models\Follow::where('follower_id', $authUser->id)
                ->where('followed_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
        }

        $canView = !$user->private_profile || $isOwnProfile || $isFollowing;

        if (!$canView || $trip->user_id !== $user->id) {
            abort(403);
        }

        $trip->load(['places.photos', 'places.hashtags']);

        return Inertia::render('Profile/Public/TripDetails', [
            'trip' => [
                'id' => $trip->id,
                'title' => $trip->title,
                'username' => $user->username,
                'image' => $trip->image ? "/storage/{$trip->image}" : null,
                'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y'),
                'places' => $trip->places->map(fn ($place) => [
                    'id' => $place->id,
                    'name' => $place->name,
                    'address' => $place->address,
                    'lat' => $place->lat,
                    'lng' => $place->lng,
                    'firstPhoto' => $place->photos->first()?->path ? '/storage/' . $place->photos->first()->path : null,
                    'photos' => $place->photos->map(fn ($p) => '/storage/' . $p->path),
                    'hashtags' => $place->hashtags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ]),
                ])
            ]
        ]);
    }

    public function publicTripPlace(string $username, Trip $trip, Place $place)
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isFollowing = false;

        if (!$isOwnProfile) {
            $isFollowing = \App\Models\Follow::where('follower_id', $authUser?->id)
                ->where('followed_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
        }

        $canView = !$user->private_profile || $isOwnProfile || $isFollowing;

        if (!$canView) {
            abort(403, 'Questo profilo è privato.');
        }

        if ($trip->user_id !== $user->id || $place->trip_id !== $trip->id) {
            abort(404, 'Viaggio o luogo non valido.');
        }

        $place->load(['photos', 'hashtags']);

        return Inertia::render('Profile/Public/TripPlaceDetails', [
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name
            ],
            'trip' => [
                'id' => $trip->id,
                'title' => $trip->title,
                'start_date' => Carbon::parse($trip->start_date)->format('d/m/Y'),
                'end_date' => Carbon::parse($trip->end_date)->format('d/m/Y')
            ],
            'place' => [
                'id' => $place->id,
                'name' => $place->name,
                'address' => $place->address,
                'lat' => $place->lat,
                'lng' => $place->lng,
                'photos' => $place->photos->map(fn ($p) => "/storage/{$p->path}"),
                'hashtags' => $place->hashtags->map(fn ($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ])
            ]
        ]);
    }

    public function profilePhoto(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        if (!$user->profile_photo || !Storage::disk('public')->exists($user->profile_photo)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($user->profile_photo));
    }

}
