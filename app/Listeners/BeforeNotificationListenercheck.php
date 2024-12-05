<?php

namespace App\Listeners;

use App\Events\BeforeNotificationcheck;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BeforeNotificationListenercheck
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(BeforeNotificationcheck $event)
    {
        Log::info('Before Notification Event Triggered', [
            'userIds' => $event->userIds,
            'fileId' => $event->fileId,
        ]);
    }
}
