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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->integer('subscription_plan_id')->nullable();
            $table->enum('status', ['Aktif', 'Pasif'])->default('Aktif');
            $table->text('system_instruction')->nullable()->comment('Botun kişiliği: Sen bir dişçi asistanısın vb.');
            $table->string('openai_api_key')->nullable();
            $table->boolean('use_own_key')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
