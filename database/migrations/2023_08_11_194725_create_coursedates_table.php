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

        // DoTO: bearbeiten nullable
        Schema::create('coursedates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainer_id');        //idtrainer
            $table->unsignedBigInteger('course_id');        //idkurs
            $table->unsignedBigInteger('sportSection_id');   //idgruppe
            $table->date('kurstermin')->nullable();
            $table->time('startzeit')->nullable();
            $table->time('startzeitmax')->nullable();
            $table->time('kurslaenge');

            $table->dateTime('kursstarttermin');
            $table->dateTime('kursendtermin');
            $table->dateTime('kursstartvorschlag');
            $table->dateTime('kursendvorschlag');
            $table->dateTime('kursstartvorschlagkunde')->nullable();
            $table->dateTime('kursendvorschlagkunde')->nullable();

            $table->integer('sportgeraetanzahl');

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('trainer_id')->references('id')->on('users');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('sportSection_id')->references('id')->on('sport_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('coursedates', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['sportSection_id']);
        });
        Schema::dropIfExists('coursedates');
    }
};
