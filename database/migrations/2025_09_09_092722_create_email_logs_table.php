<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email', 191);          // alamat yang dipakai saat kirim
            $table->string('stage', 100);          // contoh: "Seleksi Administrasi"
            $table->string('subject')->nullable(); // subjek yang dipakai saat kirim
            $table->boolean('success')->default(false);
            $table->text('error')->nullable();     // pesan error kalau gagal
            $table->timestamps();

            // Index supaya query cepat
            $table->index(['applicant_id', 'stage']);
            $table->index(['stage', 'success']);
            $table->index(['email', 'stage']);
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('email_logs');
    }
};
