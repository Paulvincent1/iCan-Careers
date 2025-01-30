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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->string('job_type');
            $table->string('work_arrangement');
            $table->json('location')->nullable();
            $table->string('experience');
            $table->string('hour_per_day');
            $table->string('hourly_rate');
            $table->string('salary');
            $table->string('description');
            $table->string('preferred_educational_attainment');
            $table->json('skills');
            $table->json('preferred_worker_types');
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
