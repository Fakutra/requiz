<?php

// database/migrations/2025_09_09_000001_create_technical_test_answers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('technical_test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technical_test_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();

            $table->string('answer_path');         // path PDF di storage
            $table->string('screen_record_url');   // link GDrive
            $table->timestamp('submitted_at');

            $table->decimal('score', 5, 2)->nullable();
            $table->text('keterangan')->nullable(); // catatan admin

            $table->timestamps();

            $table->index(['technical_test_schedule_id','submitted_at']);
            $table->index(['applicant_id','submitted_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('technical_test_answers');
    }
};
