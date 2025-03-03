<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Cambia il tema dell'utente autenticato.
     */
    public function change(Request $request)
    {
        $request->validate([
            'theme_id' => 'required|integer|exists:themes,id'
        ]);

        $user = Auth::user();
        $user->theme_id = $request->theme_id;
        $user->save();

        return back();
    }
    
    public function changeMapPointer(Request $request)
    {
        $request->validate([
            'map_pointer_id' => 'required|exists:map_pointers,id',
        ]);

        $user = Auth::user();
        $user->map_pointer_id = $request->map_pointer_id;
        $user->save();

        return back()->with('success', 'Segnaposto aggiornato con successo!');
    }

}
