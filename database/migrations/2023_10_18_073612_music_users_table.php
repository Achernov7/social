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
        Schema::create('music_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('music_id');
            $table->unsignedBigInteger('user_id');

            $table->index('user_id', 'music_users_user_idx');
            $table->index('music_id', 'music_users_music_idx');

            $table->foreign('user_id', 'music_users_user_fk')->on('users')->references('id')->onDelete('cascade');
            $table->foreign('music_id', 'music_users_group_fk')->on('music')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music_users');
    }
};
