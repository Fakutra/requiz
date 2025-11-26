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
        Schema::table('vendors', function (Blueprint $table) {
            // Hapus kolom PIC
            if (Schema::hasColumn('vendors', 'pic')) {
                $table->dropColumn('pic');
            }

            // Tambah kolom alamat
            $table->text('alamat')->nullable()->after('nama_vendor');

            // Rename kontak -> nomor_telepon
            if (Schema::hasColumn('vendors', 'kontak')) {
                $table->renameColumn('kontak', 'nomor_telepon');
            }

            // Tambah kolom email
            $table->string('email')->nullable()->after('nomor_telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Balikin: tambah kolom pic
            $table->string('pic')->nullable();

            // Hapus kolom alamat
            if (Schema::hasColumn('vendors', 'alamat')) {
                $table->dropColumn('alamat');
            }

            // Rename balik nomor_telepon → kontak
            if (Schema::hasColumn('vendors', 'nomor_telepon')) {
                $table->renameColumn('nomor_telepon', 'kontak');
            }

            // Hapus kolom email
            if (Schema::hasColumn('vendors', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
