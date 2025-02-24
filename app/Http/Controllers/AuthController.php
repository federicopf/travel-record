<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Login');
    }

    public function login(Request $request)
    {
        $password = $request->input('password');
    
        if ($password !== 'alessia123') {
            return redirect()->back()->withErrors(['password' => 'Password errata']);
        }
    
        Cache::put("allowed_ip_{$request->ip()}", true, now()->addHours(24));
    
        return Inertia::location(route('home'));
    }
}
