<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class InvalidateOtherSessions
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
{
    // Mengambil password dari request saat login
    $password = request()->input('password');

    if ($password) {
        // Melakukan invalidasi session di perangkat lain
        Auth::logoutOtherDevices($password);
    }
}
}
