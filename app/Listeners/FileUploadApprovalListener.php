<?php

namespace App\Listeners;

use App\Events\FileUploadPendingApproval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FileUploadApprovalListener
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
    public function handle(FileUploadPendingApproval $event): void
    {
        //
    }
}
