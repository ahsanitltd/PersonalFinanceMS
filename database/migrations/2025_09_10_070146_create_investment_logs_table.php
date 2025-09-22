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
        Schema::create('investment_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('investment_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['investment', 'partner_investment', 'due_payment', 'profit', 'loss', 'return', 'note']);

            $table->unsignedBigInteger('paid_by');
            $table->foreign('paid_by')->references('id')->on('investment_partners')->onDelete('cascade');

            $table->decimal('amount', 15, 2);

            $table->text('note')->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_logs');
    }
};
