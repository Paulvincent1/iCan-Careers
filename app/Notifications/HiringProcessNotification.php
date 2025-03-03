<?php

namespace App\Notifications;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HiringProcessNotification extends Notification
{
    use Queueable;

    public string $status;
    public string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(public JobPost $jobPost, public User $applicant, public User $employer)
    {
       $this->status = $this->jobPost->usersWhoApplied()
                        ->where('id', $this->applicant->id)
                        ->first()->pivot->status;

        if($this->status === 'Rejected'){
            $this->message = 'Your application for the' . $jobPost->job_title . 'position has been declined. Thank you for applying.';
        }else{
            $this->message = 'Congratulations! Your application for the ' . $jobPost->job_title . ' position is currently ' . $this->status . '. We will keep you updated.';
        }

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
            'status' => $this->jobPost->usersWhoApplied()->with('')->where('id', $this->applicant->id)->first()->pivot->status,
            'message' => $this->message,
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
