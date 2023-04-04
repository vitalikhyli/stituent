<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignUserToCase extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($assigner, $theuser, $case)
    {
        $this->assigner = $assigner;
        $this->user = $theuser;
        $this->case = $case;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        // https://laravel.com/docs/5.8/notifications#introduction

        return (new MailMessage)
                    ->subject($this->user->name.' has assigned you a case.')
                    ->from($this->assigner->email, $this->assigner->name)
                    ->greeting('Dear '.$this->user->name.',')
                    ->line('You have been assigned a case by '.$this->assigner->name.'.')
                    ->action('Case: '.$this->case->subject, url('http://www.communityfluency.com/'.$this->user->team->app_type.'/cases/'.$this->case->id))
                    ->line('Thank you!');

        // "When sending notifications via the mail channel, the notification system will automatically look for an email property on your notifiable entity."

        // Custom view:
        //
        // return (new MailMessage)->view(
        //     'emails.name', ['invoice' => $this->invoice]
        // );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
