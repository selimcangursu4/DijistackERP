<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('contact_id')->index();
            $table->string('session_id')->index()->nullable(); // Oturum takibi için
            $table->enum('type', ['incoming', 'outgoing']);
            $table->text('message');
            $table->string('sentiment')->nullable(); // Pozitif, Negatif, Nötr
            $table->string('tone')->nullable();      // Öfkeli, Mutlu, vb.
            $table->string('intent')->nullable();    // Satış, Destek, vb.
            $table->json('keywords')->nullable();    
            $table->tinyInteger('predicted_csat')->nullable(); // 1-5 puan
            $table->boolean('is_fallback')->default(false); // Bot anlamadıysa true
            $table->boolean('is_handover')->default(false); // İnsan istendiyse true
            $table->integer('token_usage')->default(0);     // Maliyet takibi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};