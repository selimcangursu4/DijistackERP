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
        Schema::create('technical_services', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->comment('İlgili şirket');
            $table->integer('customer_id')->comment('İlgili müşteri');
            $table->integer('product_id')->comment('İlgili ürün');
            $table->string('serial_number')->nullable()->comment('Ürün seri numarası');
            $table->integer('service_fault_category_id')->comment('Sorun türü');
            $table->text('fault_description')->nullable()->comment('Sorun açıklaması');
            $table->integer('service_priority_id')->comment('Servis önceliği');
            $table->integer('service_status_id')->comment('Servis durumu');
            $table->string('rack_section_id', 100)->nullable()->comment('Ürünün veya servisin bulunduğu raf/bölüm bilgisi');
            $table->date('estimated_completion_date')->nullable()->comment('Tahmini tamamlanma tarihi');
            $table->date('actual_completion_date')->nullable()->comment('Gerçek tamamlanma tarihi');
            $table->date('invoice_date')->nullable()->comment('Fatura tarihi');
            $table->boolean('part_required')->default(false)->comment('Parça değişimi gerekip gerekmediği');
            $table->integer('delivery_method_id')->nullable()->comment('Teslimat yöntemi');
            $table->text('notes')->nullable()->comment('Ek notlar');
            $table->integer('user_id')->comment('İşlemi yapan kullanıcı');
            $table->json('service_ticket')->nullable()->comment('Servis Etiket Detayları');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_services');
    }
};
