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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('nic_number')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->enum('verified_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('completion_step')->default('none');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
