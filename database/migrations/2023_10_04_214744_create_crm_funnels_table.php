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
        Schema::create('crm_funnels', function (Blueprint $table) {
            $table->id();
            // Tipo
            // 1 - 'Business', 2 - 'Contact', 3 - ...
            $table->char('role', 1)->default(1);
            // Nome
            $table->string('name');
            $table->string('slug')->unique();
            // Descrição
            $table->longText('description')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Status
            // 0- Inativo, 1 - Ativo
            $table->char('status', 1)->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_funnels');
    }
};
