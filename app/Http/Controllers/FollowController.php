<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FollowController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Recupera tutti gli utenti che l'utente corrente segue con status "accepted"
        $friends = $user->following()->get();

        return Inertia::render('Friends/Index', [
            'friends' => $friends
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
                    $u->is_following = $user->isFollowing($u);
                    return $u;
                });
        }

        return Inertia::render('Friends/Search', [
            'query' => $q,
            'results' => $results,
        ]);
    }


}
