<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('nik');
            $table->string('no_telp');
            $table->string('tpt_lahir');
            $table->date('tgl_lahir');
            $table->text('alamat');
            $table->enum('pendidikan', ['SMA/Sederajat', 'Diploma', 'S1', 'S2', 'S3']);
            $table->string('universitas');
            $table->string('jurusan');
            $table->string('thn_lulus')->nullable();
            $table->text('skills')->nullable();
            $table->string('cv_document');
            $table->enum('status', [
                'Seleksi Administrasi',
                'Tes Tulis',
                'Technical Test',
                'Interview',
                'Offering',
                'Tidak Lolos Seleksi Administrasi',
                'Tidak Lolos Tes Tulis',
                'Tidak Lolos Technical Test',
                'Tidak Lolos interview',
                'Menerima Offering',
                'Menolak Offering',
            ])->default('Seleksi Administrasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
