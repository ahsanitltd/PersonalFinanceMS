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
        Schema::create('investment_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Tesla, John Doe, My Land Flip
            $table->enum('type', ['individual', 'company', 'stock', 'crypto', 'real_estate', 'deal']);
            $table->string('contact')->nullable()->index(); // optional phone/email
            $table->text('description')->nullable(); // notes
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_entities');
    }
};
