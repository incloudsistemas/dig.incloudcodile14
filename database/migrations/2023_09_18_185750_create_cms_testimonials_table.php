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
        Schema::create('cms_testimonials', function (Blueprint $table) {
            $table->id();
            // Tipo
            // 1 - Texto, 2 - Imagem, 3 - Vídeo
            $table->char('role', 1)->default(1);
            // Título
            // (nos depoimentos sempre será o nome do cliente que deu o depoimento)
            $table->string('title');
            $table->string('slug')->unique();
            // Nome do cliente
            $table->string('customer_name');
            // Cargo
            $table->string('occupation')->nullable();
            // Empresa
            $table->string('company')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Em destaque? 1 - sim, 0 - não
            $table->boolean('featured')->default(0);
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
        Schema::dropIfExists('cms_testimonials');
    }
};
