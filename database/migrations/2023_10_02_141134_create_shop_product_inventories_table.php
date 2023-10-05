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
        Schema::create('shop_product_inventories', function (Blueprint $table) {
            $table->id();
            // Variante do produto
            $table->foreignId('variant_item_id');
            $table->foreign('variant_item_id')
                ->references('id')
                ->on('shop_product_variant_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Disponível
            $table->integer('available')->default(0);
            // Comprometido
            $table->integer('committed')->default(0);
            // Indisponível
                // Danificado
            $table->integer('unavailable_damaged')->default(0);
                // Controle de qualidade
            $table->integer('unavailable_quality_control')->default(0);
                // Estoque de segurança
            $table->integer('unavailable_safety')->default(0);
                // Outro
            $table->integer('unavailable_other')->default(0);
            // A ser recebido
            $table->integer('to_receive')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('shop_product_inventories');
    }
};
