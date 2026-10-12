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
       Schema::create('deductions', function (Blueprint $table) {
    $table->id();
    $table->string('deduction_id')->unique(); // auto-generated e.g. DED-20261012-0001
    $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
    $table->string('staff_name');
    $table->string('salary_number');
    $table->foreignId('department_id')->constrained();
    $table->foreignId('job_title_id')->constrained();
    $table->foreignId('deduction_type_id')->constrained();
    $table->text('reason');
    $table->decimal('amount', 15, 2);
    $table->enum('category', ['one_time', 'installment']);
    $table->date('deduction_date');
    $table->date('effective_date');
    $table->enum('status', ['pending', 'approved', 'rejected', 'applied', 'cancelled'])->default('pending');
    $table->string('supporting_document')->nullable();
    $table->boolean('employee_notified')->default(false);
    $table->boolean('employee_acknowledged')->default(false);
    $table->foreignId('created_by')->constrained('users');
    $table->string('approved_by')->nullable(); // "John Doe (HR Manager)"
    $table->date('approval_date')->nullable();
    $table->date('applied_date')->nullable();
    $table->text('remarks')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'branch_id', 'deduction_date']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
