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
                'veranstalter' => 'SUP Kurse',
                'veranstalterHeader' => 'organiser-1.jpg',
                'veranstalterBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung',
                'veranstalterBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung',
                'sportartBeschreibungLang'     => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang',
                'sportartBeschreibungKurz'     => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz',
                'keineKurse'                   => 'Zur Zeit finden keine Kurse statt',
                'veranstalterDomain'           => '127.0.0.1:8000',
                'user_id'                      => 1,
                'bearbeiter_id'                => 1
              ),
                array('id' => '2',
                    'veranstalter' => 'Ferienspass',
                    'veranstalterHeader' => 'organiser-1.jpg',
                    'veranstalterBeschreibungLang' => 'Dies ist ein Beispiel Organisator für die Langen Beschreibung',
                    'veranstalterBeschreibungKurz' => 'Dies ist ein Beispiel Organisator für die Kurzen Beschreibung',
                    'sportartBeschreibungLang'     => 'Dies ist ein Beispiel für die Sportart Beschreibung Lang',
                    'sportartBeschreibungKurz'     => 'Dies ist ein Beispiel für die Sportart Beschreibung Kurz',
                    'keineKurse'                   => 'Zur Zeit finden keine Kurse statt',
                    'veranstalterDomain'           => '127.0.0.1:9000',
                    'user_id'                      => 1,
                    'bearbeiter_id'                => 1
                )
        ]);
    }
}
