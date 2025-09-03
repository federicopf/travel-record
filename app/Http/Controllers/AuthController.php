<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

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

        $authService = new AuthService();
        
        if (!$authService->login($credentials)) {
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|min:5|max:255|unique:users|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|string|in:individual,couple',
            'partner_name' => 'nullable|string|max:255|required_if:type,couple',
        ]);

        $authService = new AuthService();
        $user = $authService->register($validated);

        return redirect()->route('home')->with('success', 'Registrazione completata con successo!');
    }


    // Logout e distruzione sessione
    public function logout(Request $request)
    {
        $authService = new AuthService();
        $authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Cambia la password dell'utente autenticato
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $authService = new AuthService();
        $user = Auth::user();

        if (!$authService->changePassword($user, $validated)) {
            throw ValidationException::withMessages([
                'current_password' => __('passwords.current'),
            ]);
        }

        return back()->with('success', 'Password aggiornata con successo!');
    }
}
