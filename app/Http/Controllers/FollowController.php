<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FollowController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Recupera gli utenti seguiti con status "accepted"
        $friends = $user->following()->get();

        // Conta le richieste di follow ricevute (status pending)
        $requestsCount = Follow::where('followed_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return Inertia::render('Friends/Index', [
            'friends' => $friends,
            'requests_count' => $requestsCount,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->query('q');
        $user = auth()->user();

        $results = collect();

        // Esegui la ricerca solo se la query è almeno di 3 caratteri
        if ($q && strlen($q) >= 3) {
            $results = User::where('username', 'like', "%{$q}%")
                ->where('id', '!=', $user->id)
                ->take(3)
                ->get()
                ->map(function ($u) use ($user) {
                    $follow = DB::table('follows')
                        ->where('follower_id', $user->id)
                        ->where('followed_id', $u->id)
                        ->first();

                    $u->status = $follow->status ?? null;

                    return $u;
                });

        }

        return Inertia::render('Friends/Search', [
            'query' => $q,
            'results' => $results,
        ]);
    }

    public function requests()
    {
        $user = auth()->user();

        // Trova tutte le richieste in attesa ricevute dall'utente loggato
        $requests = Follow::where('followed_id', $user->id)
            ->where('status', 'pending')
            ->with('follower') // eager load per recuperare i dati dell'utente che ha fatto la richiesta
            ->get()
            ->map(function ($follow) {
                return $follow->follower;
            });

        return Inertia::render('Friends/Requests', [
            'requests' => $requests
        ]);
    }

    public function accept(User $user)
    {
        $authUser = Auth::user();

        $follow = Follow::where('follower_id', $user->id)
            ->where('followed_id', $authUser->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $follow->update(['status' => 'accepted']);

        return response()->json(['status' => 'accepted']);
    }

    public function reject(User $user)
    {
        $authUser = Auth::user();

        Follow::where('follower_id', $user->id)
            ->where('followed_id', $authUser->id)
            ->where('status', 'pending')
            ->delete();

        return response()->json(['status' => 'rejected']);
    }

    public function follow(Request $request, User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Non puoi seguire te stesso.'], 400);
        }

        $already = $currentUser->following()->where('followed_id', $user->id)->first();

        if ($already) {
            return response()->json(['error' => 'Richiesta già inviata o utente già seguito.'], 400);
        }

        $status = $user->private_profile ? 'pending' : 'accepted';

        $currentUser->following()->attach($user->id, ['status' => $status]);

        return response()->json(['success' => true, 'status' => $status]);
    }

    public function unfollow(User $user)
    {
        $authUser = auth()->user();

        DB::table('follows')
            ->where('follower_id', $authUser->id)
            ->where('followed_id', $user->id)
            ->delete();

        return response()->json(['status' => 'removed']);
    }

}
