<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_reports', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('forum_post_id');
            $table->uuid('reported_by_user_id');
            $table->text('reason');
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
            $table->timestamps();

            $table->foreign('forum_post_id')->references('uuid')->on('forum_posts')->onDelete('cascade');
            $table->foreign('reported_by_user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_reports');
    }
};