<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\user  $user
     * @return void
     */
    public function creating(User $user)
    {
        $username = strtolower($user->surname . substr($user->name, 0, 3));
        $count = User::where('username', 'like', $username . '%')->count();
        $user->username = $count ? $username . ($count + 1) : $username;  
    }
}
