<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowService
{
    public function getUserFriends(User $user): array
    {
        $friends = $user->following()->get();
        $requestsCount = Follow::where('followed_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return [
            'friends' => $friends,
            'requests_count' => $requestsCount,
        ];
    }

    public function searchUsers(string $query, User $currentUser): \Illuminate\Support\Collection
    {
        if (strlen($query) < 3) {
            return collect();
        }

        return User::where('username', 'like', "%{$query}%")
            ->where('id', '!=', $currentUser->id)
            ->take(3)
            ->get()
            ->map(function ($user) use ($currentUser) {
                $follow = DB::table('follows')
                    ->where('follower_id', $currentUser->id)
                    ->where('followed_id', $user->id)
                    ->first();

                $user->status = $follow->status ?? null;

                return $user;
            });
    }

    public function getPendingRequests(User $user): \Illuminate\Support\Collection
    {
        return Follow::where('followed_id', $user->id)
            ->where('status', 'pending')
            ->with('follower')
            ->get()
            ->map(function ($follow) {
                return $follow->follower;
            });
    }

    public function acceptRequest(User $follower, User $followed): bool
    {
        $follow = Follow::where('follower_id', $follower->id)
            ->where('followed_id', $followed->id)
            ->where('status', 'pending')
            ->first();

        if (!$follow) {
            return false;
        }

        $follow->update(['status' => 'accepted']);
        return true;
    }

    public function rejectRequest(User $follower, User $followed): bool
    {
        $deleted = Follow::where('follower_id', $follower->id)
            ->where('followed_id', $followed->id)
            ->where('status', 'pending')
            ->delete();

        return $deleted > 0;
    }

    public function followUser(User $currentUser, User $targetUser): array
    {
        if ($currentUser->id === $targetUser->id) {
            return ['error' => 'Non puoi seguire te stesso.'];
        }

        $already = $currentUser->following()->where('followed_id', $targetUser->id)->first();

        if ($already) {
            return ['error' => 'Richiesta già inviata o utente già seguito.'];
        }

        $status = $targetUser->private_profile ? 'pending' : 'accepted';

        $currentUser->following()->attach($targetUser->id, ['status' => $status]);

        return ['success' => true, 'status' => $status];
    }

    public function unfollowUser(User $currentUser, User $targetUser): bool
    {
        $deleted = DB::table('follows')
            ->where('follower_id', $currentUser->id)
            ->where('followed_id', $targetUser->id)
            ->delete();

        return $deleted > 0;
    }
}
