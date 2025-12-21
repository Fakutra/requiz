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
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');

            $table->string('position')->nullable();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_field_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('seksi_id')->nullable()->constrained('seksi')->nullOnDelete();

            $table->decimal('gaji', 12, 2)->nullable();
            $table->decimal('uang_makan', 12, 2)->nullable();
            $table->decimal('uang_transport', 12, 2)->nullable();

            $table->date('kontrak_mulai')->nullable();
            $table->date('kontrak_selesai')->nullable();

            $table->string('link_pkwt')->nullable();
            $table->string('link_berkas')->nullable();
            $table->string('link_form_pelamar')->nullable();

            $table->dateTime('response_deadline')->nullable()->after('kontrak_selesai');
            $table->dateTime('responded_at')->nullable()->after('response_deadline');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
