<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FollowController extends Controller
{
    public function index()
    {
        $followService = new FollowService();
        $user = Auth::user();
        $data = $followService->getUserFriends($user);

        return Inertia::render('Friends/Index', $data);
    }

    public function search(Request $request)
    {
        $followService = new FollowService();
        $user = Auth::user();
        $query = $request->query('q');
        $results = $followService->searchUsers($query, $user);

        return Inertia::render('Friends/Search', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    public function requests()
    {
        $followService = new FollowService();
        $user = Auth::user();
        $requests = $followService->getPendingRequests($user);

        return Inertia::render('Friends/Requests', [
            'requests' => $requests
        ]);
    }

    public function accept(User $user)
    {
        $followService = new FollowService();
        $authUser = Auth::user();
        
        $followService->acceptRequest($user, $authUser);

        return response()->json(['status' => 'accepted']);
    }

    public function reject(User $user)
    {
        $followService = new FollowService();
        $authUser = Auth::user();
        
        $followService->rejectRequest($user, $authUser);

        return response()->json(['status' => 'rejected']);
    }

    public function follow(Request $request, User $user)
    {
        $followService = new FollowService();
        $currentUser = Auth::user();
        
        $result = $followService->followUser($currentUser, $user);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json($result);
    }

    public function unfollow(User $user)
    {
        $followService = new FollowService();
        $authUser = Auth::user();
        
        $followService->unfollowUser($authUser, $user);

        return response()->json(['status' => 'removed']);
    }

}
