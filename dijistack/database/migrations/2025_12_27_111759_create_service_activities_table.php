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
        Schema::create('service_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('service_id');
            $table->text('description');
            $table->integer('user_id');
            $table->integer('service_status_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_activities');
    }
};
