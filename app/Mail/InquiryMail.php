<?php

namespace App\Mail;

use App\Models\Inquiry;
use App\Models\Startup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/*
|--------------------------------------------------------------------------
| Unit IV — Laravel Mail: Investor Inquiry Email
|--------------------------------------------------------------------------
| Sent to the startup founder when an investor submits an inquiry.
*/

class InquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public Startup $startup
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📩 New Investor Inquiry for ' . $this->startup->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry',
            with: [
                'inquiry' => $this->inquiry,
                'startup' => $this->startup,
            ],
        );
    }
}
