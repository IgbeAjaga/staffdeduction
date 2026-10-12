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
        Schema::create('soft_delete_logs', function (Blueprint $table) {
    $table->id();
    $table->string('model_type');
    $table->unsignedBigInteger('model_id');
    $table->json('data');
    $table->foreignId('deleted_by')->constrained('users');
    $table->text('reason')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soft_delete_logs');
    }
};
