<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('branches', 'is_active')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasColumn('departments', 'is_active')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasColumn('job_titles', 'is_active')) {
            Schema::table('job_titles', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasColumn('deduction_types', 'is_active')) {
            Schema::table('deduction_types', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('branches', 'is_active')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('departments', 'is_active')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('job_titles', 'is_active')) {
            Schema::table('job_titles', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('deduction_types', 'is_active')) {
            Schema::table('deduction_types', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};