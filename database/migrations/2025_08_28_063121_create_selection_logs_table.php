<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('selection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('stage');               // contoh: "Seleksi Administrasi"
            $table->string('stage_key');           // contoh: "seleksi-administrasi"
            $table->enum('result', ['lolos','tidak_lolos']);
            // snapshot asal (buat rekap breakdown):
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jurusan')->nullable();
            $table->foreignId('acted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['stage_key','result']);
            $table->index(['applicant_id','stage_key']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('selection_logs');
    }
};
