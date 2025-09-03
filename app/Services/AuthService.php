<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials): bool
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        return true;
    }

    public function register(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'map_pointer_id' => 0,
            'theme_id' => 1,
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
        ];

        // Se il tipo è "couple", aggiungiamo il nome del partner
        if ($data['type'] === 'couple') {
            $userData['partner_name'] = $data['partner_name'];
        }

        $user = User::create($userData);

        Auth::login($user);

        return $user;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function changePassword(User $user, array $data): bool
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return true;
    }
}
