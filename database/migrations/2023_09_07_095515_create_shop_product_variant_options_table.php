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
        Schema::create('shop_product_variant_options', function (Blueprint $table) {
            $table->id();
            // Produto
            $table->foreignId('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('shop_products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Nome da opção
            // Ex: Tamanho, Cor…
            $table->string('name');
            // Valores da opção
            // Ex: Pequeno, Médio, Grande…
            $table->json('option_values')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('shop_product_variant_options');
    }
};
