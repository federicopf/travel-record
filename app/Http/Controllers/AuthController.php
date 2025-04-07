<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // Mostra la pagina di login
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    // Gestisce il login con username e password
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string', 
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'), 
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('home')->withHeaders(['Cache-Control' => 'no-store']);
    }

    // Mostra la pagina di registrazione
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    // Gestisce la registrazione di un nuovo utente
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|min:5|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|string|in:individual,couple',
            'partner_name' => 'nullable|string|max:255|required_if:type,couple',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'map_pointer_id' => 0,
            'theme_id' => 1,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ];

        // Se il tipo è "couple", aggiungiamo il nome del partner
        if ($request->type === 'couple') {
            $userData['partner_name'] = $request->partner_name;
        }

        $user = User::create($userData);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registrazione completata con successo!');
    }


    // Logout e distruzione sessione
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Cambia la password dell'utente autenticato
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('passwords.current'), // Usa il file di traduzione
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password aggiornata con successo!');
    }
}
