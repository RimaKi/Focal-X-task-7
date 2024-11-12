<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $uncompleted_tasks;
    protected $completed_tasks;

    /**
     * Create a new message instance.
     */
    public function __construct($uncompleted_tasks,$completed_tasks)
    {
        $this->uncompleted_tasks = $uncompleted_tasks;
        $this->completed_tasks = $completed_tasks;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Emails.report',
            with: ['uncompleted_tasks' => $this->uncompleted_tasks,'completed_tasks'=>$this->completed_tasks]
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
