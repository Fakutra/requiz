<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('applicant_id')
                ->constrained()
                ->onDelete('cascade');

            // RELASI STRUKTURAL
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seksi_id')->nullable()->constrained('seksi')->nullOnDelete();

            // KOMPENSASI
            $table->decimal('gaji', 12, 2)->nullable();
            $table->decimal('uang_makan', 12, 2)->nullable();
            $table->decimal('uang_transport', 12, 2)->nullable();

            // KONTRAK
            $table->date('kontrak_mulai')->nullable();
            $table->date('kontrak_selesai')->nullable();

            // LINK DOKUMEN
            $table->string('link_pkwt')->nullable();
            $table->string('link_berkas')->nullable();
            $table->string('link_form_pelamar')->nullable();

            // DEADLINE & RESPONSE
            $table->dateTime('response_deadline')->nullable();
            $table->dateTime('responded_at')->nullable();

            /**
             * =========================
             * METADATA KEPUTUSAN OFFERING
             * =========================
             */
            $table->enum('decision', ['accepted', 'declined'])
                ->nullable()
                ->comment('Keputusan akhir offering');

            $table->enum('decision_by', ['user', 'admin', 'vendor', 'system'])
                ->nullable()
                ->comment('Siapa yang mengambil keputusan');

            $table->string('decision_reason')
                ->nullable()
                ->comment('Alasan keputusan: manual, expired, revoked, legacy, dll');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
