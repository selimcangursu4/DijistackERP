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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('service_id')->nullable();
            $table->enum('request_type',["Müşteri Talebi","Personel Talebi"]);  // Talep turu musteri veya personel talebi
            $table->string('priority_id');   // Talep oncelik seviyesi
            $table->string('title');  // Talep basligi
            $table->text('description'); // Talep detay aciklamasi
            $table->integer('service_requests_status_id'); // Talep durum bilgisi
            $table->unsignedBigInteger('created_by');      // Kaydi sisteme giren personel
            $table->unsignedTinyInteger('rating')->nullable(); // Musteri memnuniyet puani
            $table->text('feedback')->nullable(); // Musteri geri bildirimi
            $table->integer('module_id'); // Konuyla İlgili Birim
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
