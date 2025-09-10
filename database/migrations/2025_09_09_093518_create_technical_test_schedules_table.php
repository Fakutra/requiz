<?php

// database/migrations/2025_09_09_000000_create_technical_test_schedules_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('technical_test_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();

            $table->dateTime('schedule_date');
            $table->string('zoom_link');
            $table->string('zoom_id')->nullable();
            $table->string('zoom_passcode')->nullable();
            $table->text('keterangan')->nullable();      // catatan/instruksi dari admin
            $table->dateTime('upload_deadline')->nullable(); // batas tombol upload

            $table->timestamps();
            $table->index(['position_id','schedule_date']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('technical_test_schedules');
    }
};
