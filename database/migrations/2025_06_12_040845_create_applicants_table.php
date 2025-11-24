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
            // vendor tempat kandidat akan ditempatkan jika lolos interview
            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->nullOnDelete();

            // admin yang "memilih" kandidat (setelah diskusi potential by)
            $table->foreignId('picked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('identity_num', 16);
            $table->string('phone_number', 15);
            $table->string('birthplace');
            $table->date('birthdate');
            $table->text('address');
            $table->enum('pendidikan', ['SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->string('universitas');
            $table->string('jurusan');
            $table->string('thn_lulus');
            $table->text('skills');
            $table->bigInteger('ekspektasi_gaji');
            $table->string('cv_document');
            $table->string('doc_tambahan')->nullable();
            $table->enum('status', [
                'Seleksi Administrasi',
                'Tes Tulis',
                'Technical Test',
                'Interview',
                'Offering',
                'Tidak Lolos Seleksi Administrasi',
                'Tidak Lolos Tes Tulis',
                'Tidak Lolos Technical Test',
                'Tidak Lolos Interview',
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
