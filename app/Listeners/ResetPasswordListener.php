<?php

namespace App\Listeners;

use Ichtrojan\Otp\Otp;
use App\Events\ResetPasswordEvent;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordListener
{
    private $otp;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->otp = new Otp;
    }

    /**
     * Handle the event.
     */
    public function handle(ResetPasswordEvent $event): void
    {
        $otpVar = $this->otp->generate($event->user->email,6,60);
        Mail::to($event->user->email)->send(new ResetPasswordMail($event->user,$otpVar));    }
}
