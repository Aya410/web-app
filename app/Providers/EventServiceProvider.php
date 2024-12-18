<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\BeforeNotificationSent;
use App\Events\AfterNotificationSent;
use App\Listeners\BeforeNotificationListener;
use App\Listeners\AfterNotificationListener;

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
        \App\Events\FileUploadPendingApproval::class => [
            \App\Listeners\FileUploadApprovalListener::class,
        ],
        BeforeNotificationSent::class => [
            BeforeNotificationListener::class,
        ],
        AfterNotificationSent::class => [
            AfterNotificationListener::class,
        ], 
    

        \App\Events\BeforeNotificationcheck::class => [
            \App\Listeners\BeforeNotificationListenercheck::class,
        ],
        \App\Events\AfterNotificationcheck::class => [
            \App\Listeners\AfterNotificationListenercheck::class,
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
