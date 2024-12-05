<?php

namespace App\Listeners;

use App\Events\AftereNotificationcheck;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AftereNotificationListenercheck
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

  
    public function handle(AftereNotificationcheck $event)
    {
        Log::info('After Notification Event Triggered', [
            'results' => $event->results,
            'fileId' => $event->fileId,
        ]);
    }
}
