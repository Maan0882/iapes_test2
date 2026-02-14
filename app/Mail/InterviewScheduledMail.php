<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $batch;
    public $application;

    public function __construct($batch, $application)
    {
        $this->batch = $batch;
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('Interview Scheduled')
                    ->view('emails.interview-scheduled')
                    ->with([
                        'batch' => $this->batch,
                        'application' => $this->application,
                    ]);
    }
}
