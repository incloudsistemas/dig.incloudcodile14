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
        Schema::create('shop_product_variant_items', function (Blueprint $table) {
            $table->id();
            // Produto
            $table->foreignId('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('shop_products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Nome do item (Combinação das variantes)
            $table->string('name');
            // Imagens
            $table->json('images')->nullable();
            // Ref. opções das variantes
            // option1, option2, option 3
            $table->json('options')->nullable();
            // Preço
            $table->integer('price')->nullable();
            // Comparação de preços
            $table->integer('compare_at_price')->nullable();
            // Custo por item
            $table->integer('unit_cost')->nullable();
            // SKU (Unidade de manutenção de estoque)
            $table->string('sku')->nullable();
            // Código de barras (ISBN, UPC, GTIN etc.)
            $table->string('barcode')->nullable();
            // Acompanhar quantidade (Gestão de inventário)
            // 0 - Não, 1 - Sim
            $table->boolean('inventory_management')->default(1);
            // Continuar vendendo mesmo sem estoque
            // 0 - Não, 1 - Sim
            $table->boolean('inventory_out_allowed')->default(0);
            // Quantidade em estoque
            $table->integer('inventory_quantity')->nullable();
            // Estoque de segurança
            $table->integer('inventory_security_alert')->nullable();
            // Este produto exige frete
            // 0 - Não, 1 - Sim
            $table->boolean('requires_shipping')->default(1);
            // Peso em gramas (g)
            $table->integer('weight')->nullable();
            // Dimensões em centímetros (cm)
            // height, width, length
            $table->json('dimensions')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('shop_product_variant_items');
    }
};
