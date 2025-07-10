<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_id');
            $table->date('date_of_birth')->nullable();
            $table->string('school')->nullable();
            $table->string('grade_level')->nullable();
            $table->string('learning_style')->nullable(); // e.g., visual, auditory
            $table->json('subjects_interested')->nullable(); // ['Math', 'Bio']
            $table->text('career_goals')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};

