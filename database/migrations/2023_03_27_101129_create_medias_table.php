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
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            //$table->string('cover_image')->nullable();//quitar?
            $table->string('extra_large_cover_image')->nullable();
            $table->string('large_cover_image')->nullable();
            $table->string('medium_cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('format')->nullable();
            $table->integer('episodes')->nullable();
            $table->integer('chapters')->nullable();
            $table->string('airing_status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('season', array('WINTER', 'SPRING', 'SUMMER', 'FALL'))->nullable();
            $table->integer('season_year')->nullable();
            $table->json('studios')->nullable();
            $table->string('source')->nullable();
            $table->json('genres')->nullable();
            $table->string('romaji')->nullable();
            $table->string('native')->nullable();
            $table->string('trailer')->nullable();
            $table->json('tags')->nullable();
            $table->json('external_link')->nullable();
            $table->enum('type', array('ANIME', 'MANGA'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
