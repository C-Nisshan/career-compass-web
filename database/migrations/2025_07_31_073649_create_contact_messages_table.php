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
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->uuid('user_id')->nullable();
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
        Schema::dropIfExists('contact_messages');
    }
};
