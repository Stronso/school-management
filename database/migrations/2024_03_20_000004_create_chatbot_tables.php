<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chatbot_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('user_input');
            $table->text('bot_response');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_conversations');
        Schema::dropIfExists('chatbot_questions');
    }
}; 