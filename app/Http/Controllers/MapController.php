<?php

namespace App\Http\Controllers;

use App\Services\MapService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MapController extends Controller
{
    public function index()
    {
        $mapService = new MapService();
        $places = $mapService->getUserMapData(Auth::id());

        return Inertia::render('Map', [
            'places' => $places
        ]);
    }
}
