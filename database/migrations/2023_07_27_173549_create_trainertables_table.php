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
        Schema::create('trainertables', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');     //idmitglied
            $table->unsignedBigInteger('trainertyp_id');      //idtrainer
            $table->unsignedBigInteger('sportSection_id');    //idabteilung
            $table->integer('status');         //status
            $table->integer('sichtbar');       //sichtbar

            $table->unsignedSmallInteger('autor_id');
            $table->unsignedSmallInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('trainertyp_id')->references('id')->on('trainertyps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('trainertables', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropForeign(['trainertyp_id']);
        });
        Schema::dropIfExists('trainertables');
    }
};
