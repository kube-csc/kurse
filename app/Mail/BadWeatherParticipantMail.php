<?php

namespace App\Mail;

use App\Models\Coursedate;
use App\Models\CourseParticipant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class BadWeatherParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Coursedate $coursedate,
        protected CourseParticipant $participant,
        protected Carbon $oldStart,
        protected Carbon $oldEnd,
        protected string $actionType,
        protected string $trainerMessage
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->actionType === 'cancel'
                ? 'Kursabsage wegen Schlechtwetter'
                : 'Terminverschiebung wegen Schlechtwetter',
        );
    }

    public function content(): Content
    {
        $domain = optional($this->coursedate->getOrganiserName)->veranstaltungDomain;
        $bookingUrl = $domain
            ? 'https://'.$domain.'/Kurseangebot/'.$this->coursedate->id
            : rtrim((string) config('app.url'), '/').'/Kurseangebot/'.$this->coursedate->id;

        return new Content(
            markdown: 'mail.participant.bad-weather-cancellation',
            with: [
                'coursedate' => $this->coursedate,
                'participant' => $this->participant,
                'oldStart' => $this->oldStart,
                'oldEnd' => $this->oldEnd,
                'actionType' => $this->actionType,
                'trainerMessage' => $this->trainerMessage,
                'bookingUrl' => $bookingUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
