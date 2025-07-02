<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
            subject: 'Buchungsbestätigung für den Kurs',
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
                if ($datevorher !== "") {
                    $mailtext .= "<br>"; // Zeilenumbruch zwischen Terminen
                }
                $teilnehmerAnzahl = 1;
                $mailtext .= "<b>Termin:</b> " . $coursedate->getCousename->kursName . "<br><br>";

                if (date('Y-m-d', strtotime($coursedate->kursstarttermin)) === date('Y-m-d', strtotime($coursedate->kursendtermin))) {
                    $mailtext .= "Datum: " . date('d.m.Y H:i', strtotime($coursedate->kursstarttermin)) . " Uhr bis " . date('d.m.Y H:i', strtotime($coursedate->kursendtermin)) . " Uhr<br>";
                    $mailtext .= "Dauer: " . date('H:i', strtotime($coursedate->kurslaenge)) . " Stunde(n)<br><br>";
                } else {
                    $mailtext .= "Der Aktivität findet täglich im Zeitraum von " .
                        date('d.m.Y', strtotime($coursedate->kursstarttermin)) . " bis " .
                        date('d.m.Y', strtotime($coursedate->kursendtermin)) .
                        " ab " . date('H:i', strtotime($coursedate->kursstarttermin)) . " Uhr statt.<br>";
                    $mailtext .= "Die Aktivität dauert jeweils " . date('H:i', strtotime($coursedate->kurslaenge)) . " Stunde(n).<br><br>";
                }

                $trainerNamen = "Folgende ".$coursedate->getOrganiserName->trainerUeberschrift." sind für den Termin eingetragen:<br>";

                $trainers = DB::table('coursedate_user')
                    ->join('users', 'coursedate_user.user_id', '=', 'users.id')
                    ->where('coursedate_user.coursedate_id', $coursedate->kurs_id)
                    ->get();

                foreach ($trainers as $trainer) {
                    $trainerNamen .= "<b>".$trainer->vorname . " " . $trainer->nachname . "</b><br>E-Mail: " . $trainer->email . "<br>Telefon: " . $trainer->telefon . "<br>";
                }
                $mailtext = $mailtext . $trainerNamen . "<br>";
                $mailtext = $mailtext . "Gebucht für:<br>";
                $mailtext = $mailtext . "1. Teilnehmer<br>";
            }
            else {
                $teilnehmerAnzahl ++;
                $mailtext = $mailtext . $teilnehmerAnzahl . ". Teilnehmer<br>";
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
