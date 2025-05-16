<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class LogUserLogout
{
    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $user = $event->user;

        // Find the latest user session without logout time and update it
        $session = UserSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($session) {
            $session->logout_at = now();
            $session->save();
        }
    }
}
