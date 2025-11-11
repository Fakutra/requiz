<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('narahubung', 100)->nullable(); // nama PIC
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('jam_operasional', 100)->nullable(); // contoh: "Senin–Jumat, 09.00–17.00"
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
            $table->softDeletes(); // biar aman kalau kehapus
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
