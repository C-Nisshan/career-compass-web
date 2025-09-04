<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_reports', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['forum_post_id']);
            $table->dropColumn('forum_post_id');

            // Add polymorphic fields
            $table->string('reportable_type');
            $table->uuid('reportable_id');

            // Index for polymorphic relationship
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('forum_reports', function (Blueprint $table) {
            // Reverse changes
            $table->dropIndex(['reportable_type', 'reportable_id']);
            $table->dropColumn(['reportable_type', 'reportable_id']);
            $table->uuid('forum_post_id');
            $table->foreign('forum_post_id')->references('uuid')->on('forum_posts')->onDelete('cascade');
        });
    }
};