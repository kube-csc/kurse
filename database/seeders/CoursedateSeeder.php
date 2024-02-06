<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursedateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coursedates')->delete();

        DB::table('coursedates')
            ->insert(
                [
                  array('id' => '1','trainer_id' => '3','course_id' => '1','sportSection_id' => '4','kurstermin' => '2019-05-24','startzeit' => '16:00:00','startzeitmax' => '16:00:00','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => '2019-05-11 15:00:00','updated_at' => '2019-05-11 15:00:00','deleted_at' => NULL,'kursstarttermin'  => '2050-05-24 16:00:00','kursendtermin' => '2050-05-24 17:30:00','kursstartvorschlag' => '2019-05-24 16:00:00','kursendvorschlag' => '2019-05-24 17:30:00','kursstartvorschlagkunde' => '2019-05-24 16:00:00','kursendvorschlagkunde' => '2019-05-24 17:30:00'),
                  array('id' => '2','trainer_id' => '2','course_id' => '2','sportSection_id' => '5','kurstermin' => '2019-05-11','startzeit' => '15:00:00','startzeitmax' => '15:00:00','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => '2019-05-11 15:00:00','updated_at' => '2019-05-11 15:00:00','deleted_at' => NULL,'kursstarttermin'  => '2050-05-11 15:00:00','kursendtermin' => '2050-05-11 16:30:00','kursstartvorschlag' => '2019-05-11 15:00:00','kursendvorschlag' => '2019-05-11 16:30:00','kursstartvorschlagkunde' => '2019-05-11 15:00:00','kursendvorschlagkunde' => '2019-05-11 16:30:00'),
                  array('id' => '3','trainer_id' => '2','course_id' => '3','sportSection_id' => '5','kurstermin' => '2019-05-27','startzeit' => '16:30:00','startzeitmax' => '16:30:00','kurslaenge' => '01:30:00','sportgeraetanzahl' => '6','user_id' => '1','bearbeiter_id' => '1','created_at' => '2019-05-11 15:00:00','updated_at' => '2019-05-11 15:00:00','deleted_at' => NULL,'kurssstarttermin' => '2050-05-27 16:30:00','kursendtermin' => '2050-05-27 18:00:00','kursstartvorschlag' => '2019-05-27 16:30:00','kursendvorschlag' => '2019-05-27 18:00:00','kursstartvorschlagkunde' => '2019-05-27 16:30:00','kursendvorschlagkunde' => '2019-05-27 18:00:00'),
                  array('id' => '4','trainer_id' => '3','course_id' => '1','sportSection_id' => '4','kurstermin' => '2019-05-30','startzeit' => '16:00:00','startzeitmax' => '16:00:00','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => '2019-05-11 15:00:00','updated_at' => '2019-05-11 15:00:00','deleted_at' => NULL,'kursstarttermin'  => '2050-05-30 16:00:00','kursendtermin' => '2050-05-30 17:30:00','kursstartvorschlag' => '2019-05-30 16:00:00','kursendvorschlag' => '2019-05-30 17:30:00','kursstartvorschlagkunde' => '2019-05-30 16:00:00','kursendvorschlagkunde' => '2019-05-30 17:30:00'),
                ]);
    }
}
