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
                'veranstaltungDomain'           => '127.0.0.1:8000',
                'sportartueberschrift'          => 'Sportart',
                'materialUeberschrift'          => 'Sportgeräte',
                'trainerUeberschrift'           => 'Trainer',
                'autor_id'                      => 1,
                'bearbeiter_id'                 => 1
              ),
            array('id' => '2',
                'veranstaltung' => 'Ferienspass',
                'veranstaltungDomain'           => '127.0.0.1:9000',
                'sportartueberschrift'          => 'Sportart',
                'materialUeberschrift'          => 'Sportgeräte',
                'trainerUeberschrift'           => 'Kursleiter',
                'autor_id'                      => 1,
                'bearbeiter_id'                 => 1
            )
        ]);
    }
}
