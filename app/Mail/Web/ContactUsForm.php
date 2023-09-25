<?php

namespace App\Mail\Web;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsForm extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $appName = config('app.name', 'InCloud');
        $subject = $this->data['subject'] ?? 'Fale Conosco';

        return $this->subject("[$appName] $subject")
            ->from($this->data['email'], $this->data['name'])
            ->replyTo($this->data['email'], $this->data['name'])
            ->markdown('web.emails.contact-us-form');
    }
}
