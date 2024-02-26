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

            $table->string('veranstalter');
            $table->string('veranstalterBild', 100);
            $table->text('veranstalterBeschreibungLang')->nullable();
            $table->text('veranstalterBeschreibungKurz')->nullable();
            $table->text('sportartBeschreibungLang')->nullable();
            $table->text('sportartBeschreibungKurz')->nullable();
            $table->text('keineKurse')->nullable();
            $table->string('veranstalterDomain')->nullable();

            $table->unsignedBigInteger('bearbeiter_id');
            $table->unsignedBigInteger('user_id');

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