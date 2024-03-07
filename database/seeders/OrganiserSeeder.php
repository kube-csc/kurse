<?php

namespace Database\Seeders;

use App\Models\Organiser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganiserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organisers')->delete();

        DB::table('organisers')->insert(
            [
              array('id' => '1',
                'veranstaltung' => 'SUP Kurse',
                'veranstaltungHeader' => 'organiser-1.jpg',
                'veranstaltungBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung.',
                'veranstaltungBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung.',
                'sportartueberschrift'          => 'Sportart',
                'sportartBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang.',
                'sportartBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz.',
                'keineKurse'                    => 'Zur Zeit finden keine Kurse statt.',
                'terminInformation'             => 'Dies ist ein Beispiel für die Termin Information.',
                'veranstaltungDomain'           => '127.0.0.1:8000',
                'trainerUeberschrift'           => 'Trainer',
                'autor_id'                      => 1,
                'bearbeiter_id'                 => 1
              ),
            array('id' => '2',
                'veranstaltung' => 'Ferienspass',
                'veranstaltungHeader' => 'organiser-1.jpg',
                'veranstaltungBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung.',
                'veranstaltungBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung.',
                'sportartBeschreibungLang'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang.',
                'sportartueberschrift'          => 'Sportart',
                'sportartBeschreibungKurz'      => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz.',
                'keineKurse'                    => 'Zur Zeit finden keine Kurse statt.',
                'terminInformation'             => 'Dies ist ein Beispiel für die Termin Information.',
                'veranstaltungDomain'           => '127.0.0.1:9000',
                'trainerUeberschrift'           => 'Kursleiter',
                'autor_id'                      => 1,
                'bearbeiter_id'                 => 1
            )
        ]);
    }
}
