<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courses')->delete();

        DB::table('courses')
            ->insert(
                [
                    array('id' => '1','sportSection_id' => '4','kursName' => 'SUP Schnupperkurs',  'kursBeschreibung' => 'SUP Schnupperkurs Kursbeschreibung','autor_id' => '1','bearbeiter_id' => '1','updated_at' => '2019-05-11 15:00:00','created_at' => '2019-05-11 15:00:00','deleted_at' => NULL),
                    array('id' => '2','sportSection_id' => '4','kursName' => 'SUP Schnupperkurs für Fortgeschrittene','kursBeschreibung' => 'Kajak Schnupperkurs Kursbeschreibung','autor_id' => '1','bearbeiter_id' => '1','updated_at' => '2019-05-11 15:00:00','created_at' => '2019-05-11 15:00:00','deleted_at' => NULL),
                    array('id' => '3','sportSection_id' => '5','kursName' => 'Kajak Schnupperkurs','kursBeschreibung' => 'Kajak Schnupperkurs Kursbeschreibung','autor_id' => '1','bearbeiter_id' => '1','updated_at' => '2019-05-11 15:00:00','created_at' => '2019-05-11 15:00:00','deleted_at' => NULL),
                    array('id' => '4','sportSection_id' => '5','kursName' => 'Kajak Grundkurs',    'kursBeschreibung' => 'Kajak Grundkurs Kursbeschreibung','autor_id' => '1','bearbeiter_id' => '1','updated_at' => '2019-05-11 15:00:00','created_at' => '2019-05-11 15:00:00','deleted_at' => NULL)
                ]);
    }
}
