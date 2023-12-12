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
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');

            $table->index('sender_id', 'chat_message_sender_idx');
            $table->foreign('sender_id','chat_message_sender_fk')->on('users')->references('id')->onDelete('cascade');

            $table->index('receiver_id', 'chat_message_reciever_idx');
            $table->foreign('receiver_id','chat_message_reciever_fk')->on('users')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_message_sender_idx');
            $table->dropIndex('chat_message_reciever_idx');
            $table->dropColumn('sender_id');
            $table->dropColumn('receiver_id');
        });
    }
};
