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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->smallInteger('quota');
            $table->enum('pendidikan_minimum', ['SMA/Sederajat', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']);
            
            $table->jsonb('skills')->nullable();
            $table->jsonb('requirements')->nullable();
            $table->jsonb('majors')->nullable();
            $table->jsonb('description'); // karena kamu tampilin sebagai list juga
            
            $table->date('deadline')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
