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
            $table->string('title');
            $table->longText('description');
            $table->string('cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('format');
            $table->integer('episodes')->nullable();
            $table->integer('chapters')->nullable();
            $table->string('airing_status');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('season', array('WINTER', 'SPRING', 'SUMMER', 'FALL'))->nullable();
            $table->integer('season_year');
            $table->json('studio');
            $table->string('source');
            $table->json('genres');
            $table->string('romanji')->nullable();
            $table->string('native')->nullable();
            $table->string('trailer')->nullable();
            $table->json('tags');
            $table->json('external_link')->nullable();
            $table->enum('type', array('ANIME', 'MANGA'));
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
