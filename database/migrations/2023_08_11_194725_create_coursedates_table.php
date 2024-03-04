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

        // ToDo: bearbeiten nullable
        Schema::create('coursedates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('organiser_id');

            $table->time('kurslaenge');

            $table->dateTime('kursstarttermin');
            $table->dateTime('kursendtermin');
            $table->dateTime('kursstartvorschlag');
            $table->dateTime('kursendvorschlag');
            $table->dateTime('kursstartvorschlagkunde');
            $table->dateTime('kursendvorschlagkunde');

            $table->integer('sportgeraetanzahl');

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('autor_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('coursedates', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });
        Schema::dropIfExists('coursedates');
    }
};
