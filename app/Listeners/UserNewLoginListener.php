<?php

namespace App\Listeners;

use App\Events\UserNewLoginEvent;
use App\Mail\UserNewLoginMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserNewLoginListener
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
    public function handle(UserNewLoginEvent $event): void
    {
        Mail::to($event->user->email)->send(new UserNewLoginMail($event->user));
    }
}
