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
        Schema::create('cms_post_categories', function (Blueprint $table) {
            $table->id();
            // Nome
            $table->string('name');
            $table->string('slug')->unique();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Status
            // 0- Inativo, 1 - Ativo
            $table->char('status', 1)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_post_categories');
    }
};