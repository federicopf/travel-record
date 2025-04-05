<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Mostra la schermata del profilo dell'utente.
     */
    public function index(User $user)
    {
        return Inertia::render('Profile/Index', [
            'user' => $user
        ]);
    }
}
