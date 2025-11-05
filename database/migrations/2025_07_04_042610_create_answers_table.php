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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_section_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_result_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('test_section_result_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('answer')->nullable(); // bisa "A", "B", atau "A,C" atau jawaban essay
            $table->unsignedTinyInteger('score')->nullable(); // nilai jawaban (isi setelah dinilai)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
