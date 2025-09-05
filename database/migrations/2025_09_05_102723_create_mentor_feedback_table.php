<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMentorFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('mentor_feedback', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('student_id');
            $table->uuid('mentor_id');
            $table->text('feedback');
            $table->enum('rating', ['1', '2', '3', '4', '5'])->nullable();
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();

            $table->foreign('student_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('mentor_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mentor_feedback');
    }
}