<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkerVerificationNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private User $worker, private bool $verified)
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
            'status' => $this->verified ? 'Congratulations!' : 'Verification Required',
            'message' =>  $this->verified ? "Your account is now fully verified! you can now apply for jobs." : "Unfortunately, your verification was not approved. Please check your submitted documents and try again.",
            'image' =>  $this->verified ? 'assets/congratulations.jpg' : 'assets/denied.jpg' ,
        ];
    }

    public function toBroadcast(object $notifiable)
    {
        return [
            'status' => $this->verified ? 'Congratulations!' : 'Verification Required',
            'message' =>  $this->verified ? "Your account is now fully verified! you can now apply for jobs." : "Unfortunately, your verification was not approved. Please check your submitted documents and try again.",
            'image' =>  $this->verified ? 'assets/congratulations.jpg' : 'assets/denied.jpg' ,
        ];
    }

    public function broadcastOn()
    {

        return new Channel('notification-' . $this->worker->id);

    }

    public function broadcastAs()
    {
        return 'notification.event';
    }

    public function broadcastWith()
    {
        return [
            'status' => $this->verified ? 'Congratulations!' : 'Verification Required',
            'message' =>  $this->verified ? "Your account is now fully verified! you can now apply for jobs." : "Unfortunately, your verification was not approved. Please check your submitted documents and try again.",
            'image' =>  $this->verified ? 'assets/congratulations.jpg' : 'assets/denied.jpg' ,
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
