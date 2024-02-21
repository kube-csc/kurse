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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sportSection_id');
            $table->string('kursName');
            $table->text('kursBeschreibung')->nullable();
            $table->boolean('visible')->default(true);  // true = 1 = sichtbar

            $table->unsignedBigInteger('autor_id');
            $table->unsignedBigInteger('bearbeiter_id');

            $table->SoftDeletes();
            $table->timestamps();

            $table->foreign('sportSection_id')->references('id')->on('sport_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Entferne die Foren-Keys
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['sportSection_id']);
        });
        Schema::dropIfExists('courses');
    }
};
