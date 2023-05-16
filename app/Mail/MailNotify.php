<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNotify extends Mailable
{
    use Queueable, SerializesModels;

    private $data = [];
    private $template = 'emails.index';

    /**
     * Create a new message instance.
     */
    public function __construct($data, $template= 'emails.index')
    {
        $this->data = $data;
        $this->template = $template;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Onilist verification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->from('onilist.sl@gmail.com', 'Onilist')
        ->subject($this->data['subject'])->view($this->template)->with('data', $this->data);
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
