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
        
        Schema::create('user_subscribes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('media_id');
            $table->enum('status',['WATCHING', 'PLAN TO WATCH', 'COMPLETED', 'REWATCHING', 'PAUSED', 'DROPPED'] );
            $table->integer('rate')->nullable();
            $table->integer('progress')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('rewatches')->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('favorite')->default(false);
            $table->boolean('private')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')
                ->on('users')
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
        Schema::dropIfExists('user_subscribes');
    }
};
