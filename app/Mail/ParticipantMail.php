<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $coursedates, $courseParticipants;

    /**
     * Create a new message instance.
     */
    public function __construct($coursedates, $courseParticipants)
    {
        $this->coursedates = $coursedates;
        $this->courseParticipants = $courseParticipants;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Participant Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $mailtext = "";
        $datevorher= "";
        $teilnehmerAnzahl = 1;
        foreach ($this->coursedates as $coursedate) {
            if($datevorher<>$coursedate->kurs_id) {
                $teilnehmerAnzahl = 1;
                $mailtext = $mailtext . "<b>Termin Name:</b> " . $coursedate->getCousename->kursName . "<br><br>";
                $mailtext = $mailtext . "Datum: " . date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) . " Uhr bis " . date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) . " Uhr<br>";
                $mailtext = $mailtext . "Dauer: " . date('H:i', strtotime($coursedate->kurslaenge)) . " Stunde(n)<br><br>";

                $trainerNamen = "Folgende Trainer sind für den Termin eingetragen:<br>";
                foreach ($coursedate->users as $user) {
                    $trainerNamen = $trainerNamen . $user->vorname . " " . $user->nachname . " " . $user->email . " " . $user->telefon . "<br>";
                }
                $mailtext = $mailtext . $trainerNamen . "<br>";
                $mailtext = $mailtext . "Gebucht für:<br>";
                $mailtext = $mailtext . "1. Teilnehmer:<br>";
            }
            else {
                $teilnehmerAnzahl ++;
                $mailtext = $mailtext . $teilnehmerAnzahl . ". Teilnehmer:<br>";
            }
            $datevorher = $coursedate->kurs_id;
        }

        return new Content(
            markdown: 'mail.participant.booked',
            with: [
                'mailtext' => $mailtext,
                'courseParticipants' => $this->courseParticipants,
            ],
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
