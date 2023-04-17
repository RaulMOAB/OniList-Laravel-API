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
        Schema::create('works_in', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->nullable()
                ->cascadeOnDelete();
            $table->foreignId('media_id')->nullable()
                ->cascadeOnDelete();
            $table->timestamps();
            $table->string('job')->nullable();


            $table->foreign('person_id')->references('id')
                ->on('people')
                ->cascadeOnDelete();
            $table->foreign('media_id')->references('id')
                ->on('medias')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works_in');
    }
};
