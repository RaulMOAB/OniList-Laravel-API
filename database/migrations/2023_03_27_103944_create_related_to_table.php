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
        Schema::create('related_to', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')
                ->nullable()
                ->cascadeOnDelete();
            $table->foreignId('related_media_id')
                ->nullable()
                ->cascadeonDelete();
            $table->string('relationship_type')->nullable();
            $table->timestamps();

            $table->foreign('media_id')->references('id')
                ->on('medias')
                ->cascadeOnDelete();
            $table->foreign('related_media_id')->references('id')
                ->on('medias')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('related_to');
    }
};
