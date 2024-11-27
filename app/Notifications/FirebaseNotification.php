<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FirebaseNotification extends Notification
{
    use Queueable;

    private $title;
    private $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toFirebase($notifiable)
    {
        return CloudMessage::withTarget('token', $notifiable->firebase_token)
            ->withNotification(FirebaseNotification::create($this->title, $this->body))
            ->withData(['click_action' => 'FLUTTER_NOTIFICATION_CLICK']);
    }
 

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
