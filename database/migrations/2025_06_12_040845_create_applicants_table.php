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
            $table->string('cv')->nullable();
            $table->string('doc_tambahan')->nullable();
            $table->enum('status', [
                'seleksi administrasi',
                // 'lolos seleksi administrasi',
                'tidak lolos seleksi administrasi',
                'seleksi tes tulis',
                'lolos seleksi tes tulis',
                'tidak lolos seleksi tes tulis'
            ])->default('seleksi administrasi');
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
