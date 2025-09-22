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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investment_partner_id')->constrained()->onDelete('cascade'); // Whom with you are investing

            $table->decimal('agreed_amount', 15, 2)->nullable();
            $table->decimal('amount_invested', 15, 2)->nullable();
            $table->decimal('your_due', 15, 2)->nullable();
            $table->decimal('partner_due', 15, 2)->nullable();

            $table->enum('profit_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('profit_value', 10, 2)->default(50); // % or fixed amount

            $table->enum('status', ['active', 'closed'])->default('active');
            $table->text('notes')->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Me or or user who is creating this data log
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
