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
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            // Categoria
            $table->foreignId('category_id')->nullable()->default(null);
            $table->foreign('category_id')
                ->references('id')
                ->on('shop_product_categories')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // Marca / Fornecedor
            $table->foreignId('brand_id')->nullable()->default(null);
            $table->foreign('brand_id')
                ->references('id')
                ->on('shop_product_brands')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // Nome do produto
            $table->string('name');
            $table->string('slug')->unique();
            // Subtítulo
            $table->string('subtitle')->nullable();
            //  Chamada/Descrição resumida
            $table->text('excerpt')->nullable();
            // Descrição completa/Conteúdo
            $table->longText('body')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Tags
            $table->json('tags')->nullable();
            // Este produto possui variantes
            // 1 - sim, 0 - não
            $table->boolean('has_variants')->default(0);
            // Publicar em
            $table->json('publish_on')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Em destaque? 1 - sim, 0 - não
            $table->boolean('featured')->default(0);
            // Permitir comentário? 1 - sim, 0 - não
            $table->boolean('comment')->default(0);
            // Data da publicação
            $table->timestamp('publish_at')->default(now());
            // Data de expiração
            $table->timestamp('expiration_at')->nullable();
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
        Schema::dropIfExists('shop_products');
    }
};
