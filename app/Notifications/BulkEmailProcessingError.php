<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Team;
use Carbon\Carbon;


class BulkEmailProcessingError extends Notification
{
    use Queueable;

    public $problems;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($items)
    {
        $this->problems = $items;
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
        $mail = (new MailMessage)
                    ->subject('Bulk Email Processing Issue')
                    ->from('errors@communityfluency.com', 'CF Alert')
                    ->line('Some Bulk Emails have Been Processing for Over an Hour.')
                    ->line('----');

        $teams = $this->problems->pluck('team_id')->unique();

        foreach($teams as $team_id) {
            $mail = $mail->line('# Team: '.Team::find($team_id)->name)
                         ->line('Queue Problems: '.$this->problems->where('team_id', $team_id)->count())
                         ->line('Processing since: '.Carbon::parse($this->problems->where('team_id', $team_id)->min('processing_start'))->diffForHumans());
        }

        $mail = $mail->line('----');

        return $mail;
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
