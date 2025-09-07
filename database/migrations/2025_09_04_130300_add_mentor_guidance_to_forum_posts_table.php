<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMentorGuidanceToForumPostsTable extends Migration
{
    public function up()
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->boolean('mentor_guidance')->default(false);
        });
    }

    public function down()
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn('mentor_guidance');
        });
    }
}