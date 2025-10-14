<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');          // create, update, delete, export, import, lolos, gagal
            $table->string('module');          // nama modul (Applicant, Report, Position, dsb)
            $table->string('description')->nullable(); // deskripsi tambahan
            $table->string('target')->nullable();      // objek yang dipengaruhi
            $table->string('ip_address', 45)->nullable(); // IPv4/IPv6
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
