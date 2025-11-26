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
        Schema::table('answers', function (Blueprint $table) {
            $table->unique(
                ['test_section_result_id', 'question_id', 'test_result_id'],
                'answers_section_question_result_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropUnique('answers_section_question_result_unique');
        });
    }
};
