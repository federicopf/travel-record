<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $profileService = new ProfileService();
        $profileService->updateName($user, $validated);

        return back()->with('success', 'Nome aggiornato con successo.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $profileService = new ProfileService();
        
        if (!$profileService->updatePassword($user, $validated)) {
            return back()->withErrors(['current_password' => 'La password attuale non è corretta.']);
        }

        return back()->with('success', 'Password aggiornata con successo.');
    }

    public function updatePrivacy(Request $request, User $user)
    {
        $validated = $request->validate([
            'private_profile' => ['required', 'boolean'],
        ]);

        $profileService = new ProfileService();
        $profileService->updatePrivacy($user, $validated);

        return back()->with('success', 'Impostazioni di privacy aggiornate.');
    }

    public function updatePhoto(Request $request, User $user)
    {
        $request->validate([
            'photo' => 'required|image|max:5120',
        ]);

        $profileService = new ProfileService();
        $profileService->updatePhoto($user, $request->file('photo'));

        return back()->with('success', 'Foto profilo aggiornata con successo!');
    }

}
