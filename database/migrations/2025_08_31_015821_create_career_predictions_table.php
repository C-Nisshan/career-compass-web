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
        Schema::create('career_predictions', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->text('input_text'); // Stores the user input (e.g., interests, skills, etc.)
            $table->json('recommendations'); // Stores the API response as JSON
            $table->uuid('user_id')->nullable(); // Nullable foreign key to users table
            $table->timestamp('predicted_at'); // Timestamp for when the prediction was made
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_predictions');
    }
};