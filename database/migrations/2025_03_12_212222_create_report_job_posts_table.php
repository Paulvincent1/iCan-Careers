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
        Schema::create('report_job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->foreignId('reported_job_post_id')->constrained('job_post')->cascadeOnDelete();
            $table->foreignId('complainant_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_job_posts');
    }
};
