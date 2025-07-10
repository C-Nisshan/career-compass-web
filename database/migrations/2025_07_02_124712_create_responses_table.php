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
        Schema::create('responses', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_id');
            $table->uuid('question_id');
            $table->uuid('option_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('question_id')->references('uuid')->on('questions')->onDelete('cascade');
            $table->foreign('option_id')->references('uuid')->on('question_options')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
