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
        Schema::create('personality_rules', function (Blueprint $table) {
            $table->id();
            // Persentase dalam 0–100, dua angka di belakang koma
            $table->decimal('min_percentage', 5, 2);         // inklusif (>=)
            $table->decimal('max_percentage', 5, 2)->nullable(); // inklusif (<=), null = tak berbatas atas
            $table->unsignedSmallInteger('score_value');     // contoh: 10/15/25/35/40
            $table->timestamps();

            $table->index(['min_percentage', 'max_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personality_rules');
    }
};
