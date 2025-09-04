<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_votes', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('forum_post_id');
            $table->uuid('user_id');
            $table->timestamps();

            $table->foreign('forum_post_id')->references('uuid')->on('forum_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->unique(['forum_post_id', 'user_id']); // One vote per user per post
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_votes');
    }
};