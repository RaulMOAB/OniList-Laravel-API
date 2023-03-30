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
        Schema::create('users_subscribe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->cascadeOnDelete();
            $table->foreignId('media_id')
                ->cascadeOnDelete();
            $table->string('status');
            $table->integer('rate')->nullable();
            $table->integer('progress')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('rewatches')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('favorites')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_subscribe');
    }
};
