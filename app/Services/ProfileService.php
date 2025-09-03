<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileService
{
    public function updateName(User $user, array $data): User
    {
        $user->update([
            'name' => $data['name'],
            'partner_name' => $data['partner_name'] ?? null,
        ]);

        return $user;
    }

    public function updatePassword(User $user, array $data): bool
    {
        if (!Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return true;
    }

    public function updatePrivacy(User $user, array $data): User
    {
        $user->private_profile = $data['private_profile'];
        $user->save();

        return $user;
    }

    public function updatePhoto(User $user, $photoFile): User
    {
        // Elimina la foto precedente se esiste
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Salva la nuova foto
        $path = $photoFile->store('profile_photos', 'public');

        $user->update([
            'profile_photo' => $path
        ]);

        return $user;
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
