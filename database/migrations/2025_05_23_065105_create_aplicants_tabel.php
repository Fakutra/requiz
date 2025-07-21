<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->char('nik', 16);
            $table->char('no_telp', 15);
            $table->string('tpt_lahir');
            $table->date('tgl_lahir');
            $table->text('alamat_ktp');
            $table->enum('pendidikan', ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']);
            $table->string('cv')->nullable();
            $table->string('doc_tambahan')->nullable();
            $table->text('domisili');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('applicants');
    }
};
