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
        Schema::create('sport_equipment', function (Blueprint $table) {
            $table->id();

            //$table->integer('idsportgeraet', true);
            $table->string('sportgeraet');
            $table->unsignedBigInteger('sportSection_id');
            $table->string('bild', 100);
            $table->integer('pixx')->default(0);
            $table->integer('pixy')->default(0);
            $table->date('anschafdatum');
            $table->date('verschrottdatum')->nullable();
            $table->integer('sportleranzahl')->default(0);
            $table->float('laenge');
            $table->float('breite');
            $table->float('hoehe');
            $table->float('gewicht');
            $table->float('tragkraft');
            $table->text('typ')->nullable();
            $table->string('privat', 1);
            $table->unsignedBigInteger('mitgliedprivat_id')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_equipment');
    }
};
