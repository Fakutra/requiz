<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('applicants')->delete();
        
        // ganti nama constraint kalau beda
        DB::statement('ALTER TABLE applicants DROP CONSTRAINT IF EXISTS applicants_pendidikan_check');

        // set daftar yang baru
        DB::statement("
            ALTER TABLE applicants
            ADD CONSTRAINT applicants_pendidikan_check
            CHECK (pendidikan IN ('SMA/SMK','D3','D4/S1','S2','S3'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE applicants DROP CONSTRAINT IF EXISTS applicants_pendidikan_check');
        DB::statement("
            ALTER TABLE applicants
            ADD CONSTRAINT applicants_pendidikan_check
            CHECK (pendidikan IN ('SMA/Sederajat','Diploma','S1','S2','S3'))
        ");
    }
};
