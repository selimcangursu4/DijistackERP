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
        Schema::create('service_statues', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->string('name'); // Beklemede, İşleme Alındı, Onarımda, Tamamlandı vb.
            $table->string('code')->unique(); 
            $table->boolean('is_active')->default(true); // Kullanımda mı?
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_statues');
    }
};
