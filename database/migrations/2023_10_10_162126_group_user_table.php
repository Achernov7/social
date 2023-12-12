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
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('user_id');

            $table->index('group_id', 'group_user_group_idx');
            $table->index('user_id', 'group_user_user_idx');

            $table->foreign('group_id', 'group_user_group_fk')->on('groups')->references('id')->onDelete('cascade');
            $table->foreign('user_id', 'group_user_user_fk')->on('users')->references('id')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_user');
    }
};
