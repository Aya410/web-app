<?php

namespace App\Listeners;

use App\Events\BeforeNotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BeforeNotificationListener
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
    public function handle(BeforeNotificationSent $event)
    {
        \Log::info("Sending notifications to users: ", [
            'user_ids' => $event->userIds,
            'token' => $event->token
        ]);
    }
}
