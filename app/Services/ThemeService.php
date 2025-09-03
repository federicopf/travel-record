<?php

namespace App\Services;

use App\Models\User;

class ThemeService
{
    public function changeTheme(User $user, int $themeId): User
    {
        $user->theme_id = $themeId;
        $user->save();

        return $user;
    }

    public function changeMapPointer(User $user, int $mapPointerId): User
    {
        $user->map_pointer_id = $mapPointerId;
        $user->save();

        return $user;
    }
}
