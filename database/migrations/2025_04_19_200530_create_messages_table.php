<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->string('sender_email');
            $table->string('receiver_email');
            $table->unsignedBigInteger('project_code');
            $table->text('message_body');
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('project_code')
                ->references('project_code')
                ->on('projects')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};