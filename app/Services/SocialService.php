<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;
use App\Models\Follow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SocialService
{
    public function getPublicProfile(string $username): array
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isPrivate = $user->private_profile;
        $isFollowing = $this->checkIfFollowing($authUser, $user);
        $canViewPrivate = $isOwnProfile || $isFollowing;

        if ($isPrivate && !$canViewPrivate) {
            return $this->formatPrivateProfile($user);
        }

        return $this->formatPublicProfile($user, $isOwnProfile);
    }

    public function getPublicTrip(string $username, Trip $trip): array
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isFollowing = $this->checkIfFollowing($authUser, $user);
        $canView = !$user->private_profile || $isOwnProfile || $isFollowing;

        if (!$canView || $trip->user_id !== $user->id) {
            abort(403);
        }

        $trip->load(['places.photos', 'places.hashtags']);

        return $this->formatPublicTrip($trip, $user);
    }

    public function getPublicTripPlace(string $username, Trip $trip, $place): array
    {
        $authUser = Auth::user();
        $user = User::where('username', $username)->firstOrFail();

        $isOwnProfile = $authUser && $authUser->id === $user->id;
        $isFollowing = $this->checkIfFollowing($authUser, $user);
        $canView = !$user->private_profile || $isOwnProfile || $isFollowing;

        if (!$canView) {
            abort(403, 'Questo profilo è privato.');
        }

        if ($trip->user_id !== $user->id || $place->trip_id !== $trip->id) {
            abort(404, 'Viaggio o luogo non valido.');
        }

        $place->load(['photos', 'hashtags']);

        return $this->formatPublicTripPlace($trip, $place, $user);
    }

    public function getProfilePhoto(string $username): string
    {
        $user = User::where('username', $username)->firstOrFail();

        if (!$user->profile_photo || !Storage::disk('public')->exists($user->profile_photo)) {
            abort(404);
        }

        return Storage::disk('public')->path($user->profile_photo);
    }

    private function checkIfFollowing(?User $authUser, User $targetUser): bool
    {
        if (!$authUser || $authUser->id === $targetUser->id) {
            return false;
        }

        return Follow::where('follower_id', $authUser->id)
            ->where('followed_id', $targetUser->id)
            ->where('status', 'accepted')
            ->exists();
    }

    private function formatPrivateProfile(User $user): array
    {
        return [
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'private' => true,
                'preview' => false,
            ],
            'places' => [],
            'trips' => []
        ];
    }

    private function formatPublicProfile(User $user, bool $isOwnProfile): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'private' => false,
                'preview' => $isOwnProfile,
            ],
            'places' => $this->getUserMapData($user->id),
            'trips' => $this->getUserTripData($user->id)
        ];
    }

    private function getUserMapData(int $userId): array
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
            })->values()->toArray();
    }

    private function getUserTripData(int $userId): array
    {
        return Trip::with('places.photos', 'places.hashtags')
            ->where('user_id', $userId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($trip) {
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

    private function formatPublicTrip(Trip $trip, User $user): array
    {
        return [
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
        ];
    }

    private function formatPublicTripPlace(Trip $trip, $place, User $user): array
    {
        return [
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
        ];
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
            ->map(fn ($path) => "/storage/{$path}")
            ->toArray();
    }
}
