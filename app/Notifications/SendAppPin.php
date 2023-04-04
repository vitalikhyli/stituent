<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAppPin extends Notification
{
    use Queueable;

    public $pin;

    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Community Fluency App Pin')
                    ->greeting('APP PIN: '.$this->pin)
                    ->line('Above is the pin you requested from Community Fluency.')
                    ->line('Enter this 5-digit pin in the Community Fluency App to connect your phone to your Community Fluency account. This pin is a one-time use code for registering your personal device.')
                    ->line('Please call Peri at 617.699.4553 if you have any questions or difficulty signing in.');
    }
}
