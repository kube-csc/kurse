<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sport_equipment')->delete();

        DB::table('sport_equipment')
            ->insert(
                [
                      array('id' => '1','sportgeraet' => 'Outrigger Einer','sportSection_id' => '3','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '2','sportgeraet' => 'Outrigger Zweier','sportSection_id' => '3','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '2','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '3','sportgeraet' => 'SUP Board 1','sportSection_id' => '4','bild' => 'S1.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '4','sportgeraet' => 'SUP Board 2','sportSection_id' => '4','bild' => 'S2.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '5','sportgeraet' => 'SUP Board 3','sportSection_id' => '4','bild' => 'S3.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '6','sportgeraet' => 'SUP Board 4','sportSection_id' => '4','bild' => 'S4.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '7','sportgeraet' => 'SUP Board 5','sportSection_id' => '4','bild' => 'S5.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'ACE-Tec Race','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '8','sportgeraet' => 'SUP Board 6','sportSection_id' => '4','bild' => 'S6.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'ACE-Tec Race','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '9','sportgeraet' => 'SUP Board 7','sportSection_id' => '4','bild' => 'S7.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'ACE-Tec gr&uuml;n, mehr Volumen','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '10','sportgeraet' => 'SUP Kids Board 1','sportSection_id' => '4','bild' => 'S8.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'Kids, blau, kurz, weniger Volumen','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '11','sportgeraet' => 'SUP Kids Board 2','sportSection_id' => '4','bild' => 'S9.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'Kids, blau, kurz, weniger Volumen','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '12','sportgeraet' => 'Outrigger Einer','sportSection_id' => '3','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '179','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '1','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '13','sportgeraet' => 'Outrigger Einer','sportSection_id' => '3','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '97','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '1','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '14','sportgeraet' => 'SUP sportlich Board 1','sportSection_id' => '4','bild' => 'S10.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'Raceboard TEC, Sportlich','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '15','sportgeraet' => 'SUP sportlich Board 2','sportSection_id' => '4','bild' => 'S11.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'Raceboard TEC, Sportlich','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '16','sportgeraet' => 'SUP Aufblasbar 1','sportSection_id' => '4','bild' => 'S12.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-03-22','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'MAKAIO KULA NUI 11.5 BOARDs THERMO FUSION<br>
                    Aufblasbare SUPs mit Allrounder-Eigenschaften, sie liegen wie unsere anderen Boards auch stabil im Wasser und haben hervorragende Gleiteigenschaften. Sie sind perfekt geeignet f&uuml;r l&auml;ngere Touren und entspanntes Cruisen.','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '18','sportgeraet' => 'SUP Aufblasbar 2','sportSection_id' => '4','bild' => 'S13.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-03-22','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'MAKAIO KULA NUI 11.5 BOARDs THERMO FUSION<br>
                    Aufblasbare SUPs mit Allrounder-Eigenschaften, sie liegen wie unsere anderen Boards auch stabil im Wasser und haben hervorragende Gleiteigenschaften. Sie sind perfekt geeignet f&uuml;r l&auml;ngere Touren und entspanntes Cruisen.','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '19','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '20','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '21','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '22','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '23','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '24','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '25','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '26','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '27','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '28','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '29','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                      array('id' => '30','sportgeraet' => 'FerienspaÃŸ','sportSection_id' => '1','bild' => '','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => '','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                     ]
        );
     }
}