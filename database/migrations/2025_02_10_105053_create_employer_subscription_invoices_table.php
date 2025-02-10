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
        Schema::create('employer_subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('description');
            $table->string('payment_url');
            $table->enum('subscription_type',['Free','Pro','Premium']);
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_subscription_invoices');
    }
};
