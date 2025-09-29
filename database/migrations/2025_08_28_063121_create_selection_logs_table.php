<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('selection_logs', function (Blueprint $table) {
            $table->id();

            // Siapa kandidatnya
            $table->foreignId('applicant_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nama tahap (human readable) + slug tahap (normalisasi)
            $table->string('stage', 100);      // contoh: "Seleksi Administrasi"
            $table->string('stage_key', 50);   // contoh: "seleksi-administrasi"

            // Keputusan pada tahap tsb
            // (Enum 2 nilai; kalau ingin fleksibel bisa ganti ke string(20) + validasi aplikasi)
            $table->enum('result', ['lolos', 'tidak_lolos']);

            // Snapshot untuk analitik/rekap breakdown (opsional tapi berguna)
            $table->foreignId('position_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('jurusan', 100)->nullable();

            // Siapa yang melakukan aksi (admin/user) — optional
            $table->foreignId('acted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // ===== Indexes =====
            // Index utama rekap (ambil log terbaru per applicant per stage_key)
            $table->index(
                ['stage_key', 'applicant_id', 'created_at'],
                'selection_logs_stage_app_created_idx'
            );

            // Bantu join subquery latest (MAX(created_at) by applicant)
            $table->index(
                ['applicant_id', 'created_at'],
                'selection_logs_app_created_idx'
            );

            // Agregasi cepat per stage_key & result (laporan pie/bar dsb.) — opsional tapi berguna
            $table->index(
                ['stage_key', 'result'],
                'selection_logs_stage_result_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_logs');
    }
};
