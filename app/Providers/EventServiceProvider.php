<?php

namespace App\Providers;

use App\Events\UserNewLoginEvent;
use App\Events\ResetPasswordEvent;
use Illuminate\Support\Facades\Event;
use App\Events\EmailVerificationEvent;
use Illuminate\Auth\Events\Registered;
use App\Listeners\UserNewLoginListener;
use App\Listeners\ResetPasswordListener;
use App\Listeners\EmailVerificationListener;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserNewLoginEvent::class=>[
            UserNewLoginListener::class,
        ],
        EmailVerificationEvent::class => [
            EmailVerificationListener::class,
        ],
        ResetPasswordEvent::class => [
            ResetPasswordListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
