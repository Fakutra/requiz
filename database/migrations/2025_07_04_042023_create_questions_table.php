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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['PG', 'Multiple', 'Poin', 'Essay']);
            $table->string('category');
            $table->text('question');

            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->string('option_e')->nullable();

            // poin tiap pilihan (khusus soal tipe "poin")
            $table->tinyInteger('point_a')->nullable();
            $table->tinyInteger('point_b')->nullable();
            $table->tinyInteger('point_c')->nullable();
            $table->tinyInteger('point_d')->nullable();
            $table->tinyInteger('point_e')->nullable();

            $table->string('answer')->nullable(); // contoh: "A" untuk PG, atau "A,B,C" untuk multiple
            $table->string('image_path')->nullable(); // jika soal punya gambar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
