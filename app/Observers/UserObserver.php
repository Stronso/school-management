<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
       // Only force role to 'student' if role is not set or is not 'admin' or 'teacher'
       if (!isset($user->role) || !in_array($user->role, ['admin', 'teacher'])) {
        $user->role = 'student';}
    }

}
