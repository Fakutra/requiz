<?php

// database/migrations/2025_09_11_000000_create_interview_schedules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();

            // Rentang waktu interview (mis. 2025-09-11 14:00 s.d. 16:00)
            $table->dateTime('schedule_start');   // mulai
            $table->dateTime('schedule_end');     // selesai

            // Info meeting (opsional)
            $table->string('zoom_link')->nullable();
            $table->string('zoom_id')->nullable();
            $table->string('zoom_passcode')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->index(['position_id', 'schedule_start']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('interview_schedules');
    }
};
