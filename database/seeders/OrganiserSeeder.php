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
                'veranstalterBild' => 'organiser-1.jpg',
                'veranstalterBeschreibung' => 'Dies ist ein Beispiel Organisator',
                'veranstalterrDomain' => 'http://127.0.0.1:8000/',
                'user_id' => 1,
                'bearbeiter_id' => 1
              )
        ]);
    }
}
