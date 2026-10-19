<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deduction_installments', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('amount');
            // pending | approved | applied
            $table->timestamp('applied_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('deduction_installments', function (Blueprint $table) {
            $table->dropColumn(['status', 'applied_at']);
        });
    }
};