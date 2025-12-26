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
        Schema::create('service_warranties', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('product_id');
            $table->string('imei')->unique();
            $table->date('invoice_date');
            $table->date('warranty_end_date');
            $table->enum('warranty_status',["Garanti Var","Garanti Yok"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_warranties');
    }
};
