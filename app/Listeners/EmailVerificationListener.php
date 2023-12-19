<?php

namespace App\Listeners;

use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Mail;
use App\Events\EmailVerificationEvent;
use App\Mail\EmailVerificationMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerificationListener
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
    public function handle(EmailVerificationEvent $event): void
    {
        $otpVar = $this->otp->generate($event->user->email,6,60);
        Mail::to($event->user->email)->send(new EmailVerificationMail($event->user,$otpVar));
    }
}
