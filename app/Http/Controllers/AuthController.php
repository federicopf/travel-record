<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        if ($password === 'alessia123') {
            Cache::put("allowed_ip_{$request->ip()}", true, now()->addHours(24));
            return redirect('/');
        }

        return back()->withErrors(['password' => 'Password errata']);
    }

    public function logout()
    {
        Cache::forget("allowed_ip_" . request()->ip());
        return redirect('/login');
    }
}
