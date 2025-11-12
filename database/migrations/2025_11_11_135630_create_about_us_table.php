// database/migrations/2025_11_11_000000_create_about_us_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->text('description');                // teks tentang kami
            $table->string('image_path')->nullable();   // storage path
            $table->enum('layout', ['image_left', 'image_right', 'full_image'])
                  ->default('image_left');              // pengaturan layout
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('about_us');
    }
};
