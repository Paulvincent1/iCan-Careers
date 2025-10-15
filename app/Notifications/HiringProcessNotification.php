<?php

namespace App\Notifications;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HiringProcessNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public string $status;
    public string $message;
    public ?string $image;

    /**
     * Create a new notification instance.
     */
    public function __construct(public JobPost $jobPost, public User $applicant)
    {
        $this->status = $this->jobPost->usersWhoApplied()
            ->where('users.id', $this->applicant->id)  // Qualify the 'id' column to avoid ambiguity
            ->first()
            ->pivot
            ->status;

        if($this->status === 'Rejected'){
            $this->message = 'Your application for the ' . $jobPost->job_title . ' position has been declined. Thank you for applying.';
        }else{
            $this->message =  'Congrats! Your application for
            the ' . $jobPost->job_title .
           ' position is currently '
            . $this->status . ', ' . 'We\'ll keep you
            updated.';
        }

        $this->image = $this->jobPost->load('employer.employerProfile.businessInformation')
        ->employer->employerProfile?->businessInformation->business_logo ?? null;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast','database'];
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
            'status' => $this->jobPost->usersWhoApplied()
            ->where('users.id', $this->applicant->id)
            ->first()
            ->pivot
            ->status,
            'message' => $this->message,
            'image' => $this->image
        ];
    }

    public function toBroadcast()
    {
        return new BroadcastMessage([
            'status' => $this->jobPost->usersWhoApplied()
            ->where('users.id', $this->applicant->id)
            ->first()
            ->pivot
            ->status,
            'message' => $this->message,
            'image' => $this->image
        ]);
    }

    public function broadcastOn()
    {

        return new Channel('notification-' . $this->applicant->id);

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
