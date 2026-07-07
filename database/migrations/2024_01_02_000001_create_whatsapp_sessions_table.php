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
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->enum('status', ['disconnected', 'connecting', 'connected', 'error'])->default('disconnected');
            $table->text('qr_code')->nullable();
            $table->timestamp('qr_expires_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->text('session_data')->nullable(); // encrypted
            $table->text('error_message')->nullable();
            $table->timestamps();

            // One session per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
