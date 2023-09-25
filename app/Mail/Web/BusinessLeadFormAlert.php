<?php

namespace App\Mail\Web;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BusinessLeadFormAlert extends Mailable
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
        $subject = $this->data['subject'] ?? 'Nova Conversão Registrada';

        return $this->subject("[$appName] $subject")
            ->from($this->data['email'], $this->data['name'])
            ->replyTo($this->data['email'], $this->data['name'])
            ->markdown('web.emails.business-form-alert');
    }
}
