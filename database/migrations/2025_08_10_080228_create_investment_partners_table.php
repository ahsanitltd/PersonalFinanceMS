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
        Schema::create('investment_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('contact')->nullable()->index();
            $table->text('description')->nullable();

            $table->enum('type', ['individual', 'company', 'stock', 'crypto', 'real_estate', 'deal']);
            $table->foreignId('company_id')->nullable();

            $table->double('due', 2)->nullable();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_partners');
    }
};
