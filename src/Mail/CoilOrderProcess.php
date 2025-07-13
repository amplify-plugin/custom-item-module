<?php

namespace Amplify\System\CustomItem\Mail;

use App\Models\EventTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CoilOrderProcess extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $name, public EventTemplate $data) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $address = config('mail.from.name');

        return new Envelope(
            from: new Address($address, 'Jeffrey Way'),
            subject: $this->data->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'custom-item::evaporator_coil_text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(storage_path($this->name)),
        ];
    }
}
