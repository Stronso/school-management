<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class LogUserLogin
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        // Create a new user session record with login time
        UserSession::create([
            'user_id' => $user->id,
            'login_at' => now(),
        ]);
    }
}
