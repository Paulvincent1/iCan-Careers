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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->string('external_id');
            $table->string('description')->nullable();
            $table->decimal('amount',10,2);   
            $table->json('items');
            $table->string('invoice_url');
            $table->enum('status', ['PENDING', 'PAID', 'EXPIRED','SETTLED'])->default('PENDING');
            $table->foreignId('worker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
