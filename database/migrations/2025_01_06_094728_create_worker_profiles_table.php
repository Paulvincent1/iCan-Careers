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
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->text('profile_description');
            $table->string('highest_educational_attainment');
            $table->string('job_type');
            $table->string('work_hour_per_day');
            $table->string('hour_pay');
            $table->string('month_pay');
            $table->string('birth_year');
            $table->string('gender');
            $table->string('resume')->nullable();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_profiles');
    }
};
