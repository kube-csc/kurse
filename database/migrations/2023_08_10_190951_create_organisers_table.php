<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organisers', function (Blueprint $table) {
            $table->id();

            $table->string('veranstaltung');
            $table->string('veranstaltungDomain')->nullable();
            $table->string('veranstaltungHeader')->nullable();

            //ToDo: Die Daten werden n der Tabelle "Organiserinformations" gespeichert
            //$table->text('veranstaltungBeschreibungLang')->nullable();
            //$table->text('veranstaltungBeschreibungKurz')->nullable();
            $table->string('sportartUeberschrift')->nullable();;
            //$table->text('sportartBeschreibungLang')->nullable();
            //$table->text('sportartBeschreibungKurz')->nullable();
            $table->string('materialUeberschrift')->nullable();;
            //$table->text('materialBeschreibungLang')->nullable();
            //$table->text('materialBeschreibungKurz')->nullable();
            $table->string('trainerUeberschrift')->nullable();;
            //$table->text('keineKurse')->nullable();
            //$table->text('terminInformation')->nullable();

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('autor_id');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisers');
    }
};
