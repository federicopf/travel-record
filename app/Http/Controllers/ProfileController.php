<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

    public function updateName(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'partner_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Nome aggiornato con successo.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'La password attuale non è corretta.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password aggiornata con successo.');
    }

    public function updatePrivacy(Request $request, User $user)
    {
        $validated = $request->validate([
            'private_profile' => ['required', 'boolean'],
        ]);

        $user->private_profile = $validated['private_profile'];
        $user->save();

        return back()->with('success', 'Impostazioni di privacy aggiornate.');
    }
}
