<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_id');
            $table->string('profession_title')->nullable();
            $table->string('industry')->nullable(); // e.g., IT, Medicine
            $table->integer('experience_years')->nullable();
            $table->text('bio')->nullable();
            $table->json('areas_of_expertise')->nullable(); // ['AI', 'HR']
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('availability')->nullable(); // e.g., Mon-Fri evenings
            $table->timestamps();

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_profiles');
    }
};

