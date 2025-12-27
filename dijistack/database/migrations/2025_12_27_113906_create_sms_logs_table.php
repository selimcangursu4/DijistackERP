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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->comment('İlgili şirket');
            $table->integer('module_id')->comment('SMS hangi modülden gönderildi');
            $table->integer('module_record_id')->nullable()->comment('Modüle ait kayıt ID');
            $table->string('recipient_name')->nullable()->comment('Alıcı adı');
            $table->string('recipient_phone')->comment('Alıcı telefon numarası');
            $table->text('message')->comment('Gönderilen SMS içeriği');
            $table->unsignedBigInteger('sent_by')->nullable()->comment('SMS’i gönderen kullanıcı ID');
            $table->enum('status', ['Beklemede', 'Gönderildi', 'Hata'])->default('Beklemede');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
