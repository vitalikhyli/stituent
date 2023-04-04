<?php

namespace App\Mail\Marketing\NewCandidate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// https://laravel.com/docs/7.x/mail#generating-markdown-mailables

class IntroEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('peri@communityfluency.com', "Peri O'Connor")
                    ->subject('Congratulations on becoming a candidate')
                    ->markdown('emails.marketing.new_candidate.intro',
                                ['candidate' => $this->candidate]
                              );
    }
}
