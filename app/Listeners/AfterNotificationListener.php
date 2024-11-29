<?php

namespace App\Listeners;

use App\Events\AfterNotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AfterNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(AfterNotificationSent $event)
    {
        \Log::info("Notification sending results: ", [
            'results' => $event->results
        ]);
    }
}
