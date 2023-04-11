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
        Schema::create('person_dubs_character', function (Blueprint $table) {
            $table->id();
            $table->foreignId('people_id')
                ->cascadeOnDelete();
            $table->foreignId('character_id')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('people_id')->references('id')
                ->on('people')
                ->cascadeOnDelete();
            $table->foreign('character_id')->references('id')
                ->on('characters')
                ->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_dubs_character');
    }
};
