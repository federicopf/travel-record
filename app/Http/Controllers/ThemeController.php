<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Cambia il tema dell'utente autenticato.
     */
    public function change(Request $request)
    {
        $validated = $request->validate([
            'theme_id' => 'required|integer|exists:themes,id'
        ]);

        $themeService = new ThemeService();
        $user = Auth::user();
        $themeService->changeTheme($user, $validated['theme_id']);

        return back();
    }
    
    public function changeMapPointer(Request $request)
    {
        $validated = $request->validate([
            'map_pointer_id' => 'required|exists:map_pointers,id',
        ]);

        $themeService = new ThemeService();
        $user = Auth::user();
        $themeService->changeMapPointer($user, $validated['map_pointer_id']);

        return back()->with('success', 'Segnaposto aggiornato con successo!');
    }

}
