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
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('name')->nullable()->after('position_id');
            $table->string('email')->nullable()->after('name');
            $table->string('nik', 20)->nullable()->after('email');
            $table->string('no_telp', 20)->nullable()->after('nik');
            $table->string('tpt_lahir')->nullable()->after('no_telp');
            $table->date('tgl_lahir')->nullable()->after('tpt_lahir');
            $table->text('alamat')->nullable()->after('tgl_lahir');
            $table->string('ekspektasi_gaji')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'email',
                'nik',
                'no_telp',
                'tpt_lahir',
                'tgl_lahir',
                'alamat',
                'ekspektasi_gaji'
            ]);
        });
    }
};
