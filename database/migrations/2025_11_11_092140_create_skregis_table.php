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
        Schema::create('skregis', function (Blueprint $table) {
            $table->id();
            $table->enum('content', ['judul', 'list']); // tipe form
            $table->string('title')->nullable(); // cuma kepake kalau list
            $table->longText('description'); // semua isi masuk sini
            // $table->integer('order')->default(0); // buat urutan tampil
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skregis');
    }
};
