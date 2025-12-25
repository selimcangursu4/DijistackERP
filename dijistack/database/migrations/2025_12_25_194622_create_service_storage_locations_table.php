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
        Schema::create('service_storage_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('rack');
            $table->string('shelf');
            $table->string('bin');
            $table->enum('status', ['Aktif', 'Pasif'])
            ->default('Aktif');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_storage_locations');
    }
};
