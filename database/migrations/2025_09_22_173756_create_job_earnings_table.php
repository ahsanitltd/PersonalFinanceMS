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
        Schema::create('job_earnings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);             // How much earned
            $table->string('currency')->default('BDT'); // For future multi-currency
            $table->date('earn_month')->nullable();  // Month of earning (e.g. 2025-09-01)
            $table->boolean('is_paid')->default(true);   // Mark paid/unpaid
            $table->date('paid_at')->nullable();          // When paid
            $table->text('notes')->nullable();            // Optional notes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_earnings');
    }
};
