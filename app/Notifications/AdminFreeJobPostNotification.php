<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminFreeJobPostNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private User $admin, private User $employer)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toDatabase()
    {
        return [
            'status' => 'Job Post Review',
            'message' => $this->employer->name . ' has posted a job.',
            'image' => $this->employer->profile_img,
        ];
    }

    public function toBroadcast()
    {
        return [
            'status' => 'Job Post Review',
            'message' => $this->employer->name . ' has posted a job.',
            'image' => $this->employer->profile_img,
        ];
    }

    public function broadcastOn()
    {

        return new Channel('notification-' . $this->admin->id);

    }

    public function broadcastAs()
    {
        return 'notification.event';
    }

    public function broadcastWith()
    {
        return [
            'status' => 'Job Post Review',
            'message' => $this->employer->name . ' has posted a job.',
            'image' => $this->employer->profile_img,
        ];
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
