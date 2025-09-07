<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->enum('status', ['active', 'hidden'])->default('active')->after('body');
            $table->boolean('pinned')->default(false)->after('status');
        });

        Schema::table('forum_comments', function (Blueprint $table) {
            $table->enum('status', ['active', 'hidden'])->default('active')->after('comment');
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn(['status', 'pinned']);
        });

        Schema::table('forum_comments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};