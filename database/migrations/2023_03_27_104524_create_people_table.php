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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('romaji');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('date_of_death')->nullable();
            $table->integer('age')->nullable();
            $table->string('years_active')->nullable();
            $table->string('home_town')->nullable();
            $table->string('blood_type')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_large')->nullable();
            $table->string('image_medium')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
