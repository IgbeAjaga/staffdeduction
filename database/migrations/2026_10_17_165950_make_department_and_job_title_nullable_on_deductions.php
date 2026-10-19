<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys first
        Schema::table('deductions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['job_title_id']);
        });

        // Make columns nullable
        DB::statement('ALTER TABLE deductions MODIFY department_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE deductions MODIFY job_title_id BIGINT UNSIGNED NULL');

        // Re-add foreign keys with nullOnDelete
        Schema::table('deductions', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();

            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deductions', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['job_title_id']);
        });

        DB::statement('ALTER TABLE deductions MODIFY department_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE deductions MODIFY job_title_id BIGINT UNSIGNED NOT NULL');

        Schema::table('deductions', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->cascadeOnDelete();

            $table->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->cascadeOnDelete();
        });
    }
};