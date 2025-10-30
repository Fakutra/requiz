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
        $applicantDrops = collect(['nik', 'name', 'email', 'no_telp', 'tpt_lahir', 'tgl_lahir', 'alamat'])
            ->filter(fn($col) => Schema::hasColumn('applicants', $col))
            ->values()
            ->all();

        if (!empty($applicantDrops)) {
            Schema::table('applicants', function (Blueprint $table) use ($applicantDrops) {
                $table->dropColumn($applicantDrops);
            });
        }

        /**
         * positions: tambah kolom baru
         * (tipe bisa lo adjust nanti kalau perlu stricter/enum/JSONB)
         */
        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'min_edu')) {
                $table->string('min_edu', 50)->nullable()->after('description');
            }
            if (!Schema::hasColumn('positions', 'required_skills')) {
                $table->json('required_skills')->nullable()->after('min_edu'); // array skill
            }
            if (!Schema::hasColumn('positions', 'related_degree')) {
                $table->string('related_degree', 120)->nullable()->after('required_skills');
            }
            if (!Schema::hasColumn('positions', 'general_requirement')) {
                $table->text('general_requirement')->nullable()->after('related_degree');
            }
            if (!Schema::hasColumn('positions', 'job_field')) {
                $table->string('job_field', 100)->nullable()->after('general_requirement');
            }
        });

        /**
         * users: tambah data personal yang tadinya di applicants
         */
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'identity_num')) {
                $table->string('identity_num', 25)->nullable()->unique()->after('email'); // NIK unik, tapi nullable biar migrasi aman
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 25)->nullable()->after('identity_num');
            }
            if (!Schema::hasColumn('users', 'birthplace')) {
                $table->string('birthplace', 100)->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('birthplace');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('birthdate');
            }
        });

        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'additional_doc')) {
                $table->string('additional_doc')->nullable()->after('cv_document');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'nik')) {
                $table->string('nik', 25)->nullable()->after('id');
            }
            if (!Schema::hasColumn('applicants', 'name')) {
                $table->string('name', 120)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('applicants', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('applicants', 'no_telp')) {
                $table->string('no_telp', 25)->nullable()->after('email');
            }
            if (!Schema::hasColumn('applicants', 'tpt_lahir')) {
                $table->string('tpt_lahir', 100)->nullable()->after('no_telp');
            }
            if (!Schema::hasColumn('applicants', 'tgl_lahir')) {
                $table->date('tgl_lahir')->nullable()->after('tpt_lahir');
            }
            if (!Schema::hasColumn('applicants', 'alamat')) {
                $table->text('alamat')->nullable()->after('tgl_lahir');
            }
        });

        /**
         * positions: hapus kolom baru
         */
        $positionDrops = collect(['min_edu','required_skills','related_degree','general_requirement','job_field'])
            ->filter(fn ($col) => Schema::hasColumn('positions', $col))
            ->values()
            ->all();

        if (!empty($positionDrops)) {
            Schema::table('positions', function (Blueprint $table) use ($positionDrops) {
                $table->dropColumn($positionDrops);
            });
        }

        /**
         * users: hapus kolom personal
         */
        $userDrops = collect(['nik','no_telp','tpt_lahir','tgl_lahir','alamat'])
            ->filter(fn ($col) => Schema::hasColumn('users', $col))
            ->values()
            ->all();

        if (!empty($userDrops)) {
            Schema::table('users', function (Blueprint $table) use ($userDrops) {
                $table->dropColumn($userDrops);
            });
        }
    }
};
