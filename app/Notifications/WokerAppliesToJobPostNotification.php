<?php

namespace App\Notifications;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WokerAppliesToJobPostNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $applicant, public User $employer, public JobPost $jobPost)
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
        return ['database','broadcast'];
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

    public function toDatabase(object $notifiable)
    {
        return [
            'status' => $this->applicant->name,
            'message' => $this->applicant->name . 'applies to your job post ' .$this->jobPost . '.',
            'image' => $this->applicant->profile_img,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return [
            'status' => $this->applicant->name,
            'message' => $this->applicant->name . 'applies to your job post ' . $this->jobPost . '.',
            'image' => $this->applicant->profile_img,
        ];
    }

    public function broadcastOn()
    {

        return new Channel('notification-' . $this->employer->id);

    }

    public function broadcastAs()
    {
        return 'notification.event';
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
