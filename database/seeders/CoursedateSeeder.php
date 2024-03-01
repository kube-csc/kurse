<?php

namespace Database\Seeders;

use Carbon\Carbon;
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

        $now = Carbon::now();
        $kursterminTime15 = Carbon::now()->addDays(10)->addHour(15)->format('Y-m-d H:i:');
        $kursterminTime16 = Carbon::now()->addDays(10)->addHour(16)->format('Y-m-d H:i:');
        $kursterminTime16_30 = Carbon::now()->addDays(10)->addHour(16)->addMinutes(30)->format('Y-m-d H:i:');
        $kursterminTime17_30 = Carbon::now()->addDays(10)->addHour(17)->addMinutes(30)->format('Y-m-d H:i:');

        // UpdateorgID: Anpassung von sportSection_id auf organiser_id  sportSection_id kann entfallen

        DB::table('coursedates')
             ->insert(
                [
                    array('id' => '1','course_id' => '1','organiser_id' => '1','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => $now,'updated_at' => $now,'deleted_at' => NULL,'kursstarttermin' => $kursterminTime15,'kursendtermin' => $kursterminTime16_30,'kursstartvorschlag' => '2019-05-11 15:00:00','kursendvorschlag' => '2019-05-11 16:30:00','kursstartvorschlagkunde' => '2019-05-11 15:00:00','kursendvorschlagkunde' => '2019-05-11 16:30:00'),
                    array('id' => '2','course_id' => '1','organiser_id' => '1','kurslaenge' => '01:30:00','sportgeraetanzahl' => '6','user_id' => '1','bearbeiter_id' => '1','created_at' => $now,'updated_at' => $now,'deleted_at' => NULL,'kursstarttermin' => $kursterminTime16,'kursendtermin' => $kursterminTime17_30,'kursstartvorschlag' => '2019-05-27 16:30:00','kursendvorschlag' => '2019-05-27 18:00:00','kursstartvorschlagkunde' => '2019-05-27 16:30:00','kursendvorschlagkunde' => '2019-05-27 18:00:00'),
                    array('id' => '3','course_id' => '2','organiser_id' => '1','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => $now,'updated_at' => $now,'deleted_at' => NULL,'kursstarttermin' => $kursterminTime15,'kursendtermin' => $kursterminTime16_30,'kursstartvorschlag' => '2019-05-30 16:00:00','kursendvorschlag' => '2019-05-30 17:30:00','kursstartvorschlagkunde' => '2019-05-30 16:00:00','kursendvorschlagkunde' => '2019-05-30 17:30:00'),
                    array('id' => '4','course_id' => '3','organiser_id' => '2','kurslaenge' => '01:30:00','sportgeraetanzahl' => '0','user_id' => '1','bearbeiter_id' => '1','created_at' => $now,'updated_at' => $now,'deleted_at' => NULL,'kursstarttermin' => $kursterminTime16,'kursendtermin' => $kursterminTime17_30,'kursstartvorschlag' => '2019-05-30 16:00:00','kursendvorschlag' => '2019-05-30 17:30:00','kursstartvorschlagkunde' => '2019-05-30 16:00:00','kursendvorschlagkunde' => '2019-05-30 17:30:00'),
                ]);

    }
}
