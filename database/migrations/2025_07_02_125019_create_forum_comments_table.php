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
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('forum_post_id');
            $table->uuid('user_id');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('forum_post_id')->references('uuid')->on('forum_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_comments');
    }
};
