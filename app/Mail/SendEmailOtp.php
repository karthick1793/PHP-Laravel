<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public $OTP;

    public $name;

    public $expiryTime;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $OTP, $name, $expiryTime)
    {
        $this->email = $email;
        $this->OTP = $OTP;
        $this->name = $name;
        $this->expiryTime = $expiryTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your One-Time Password (OTP) for Secure Login',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
