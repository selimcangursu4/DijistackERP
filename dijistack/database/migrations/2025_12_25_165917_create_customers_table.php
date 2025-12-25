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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('fullname');
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('phone_secondary')->nullable();
            $table->string('address')->nullable();
            $table->string('country_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('district_id')->nullable();
            $table->string('postal_code');
            $table->string('tax_office')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('customer_type_id');
            $table->integer('customer_status_id');
            $table->text('notes')->nullable();
            $table->integer('created_by');
            $table->integer('customer_preferred_contact_method_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
