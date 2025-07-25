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
        Schema::create('test_section_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_section_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable(); // Skor per section
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_section_results');
    }
};
