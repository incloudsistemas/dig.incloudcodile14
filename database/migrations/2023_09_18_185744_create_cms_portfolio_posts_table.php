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
        Schema::create('cms_portfolio_posts', function (Blueprint $table) {
            $table->id();
            // Título
            $table->string('title');
            $table->string('slug')->unique();
            // Subtítulo
            $table->string('subtitle')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Tags
            $table->json('tags')->nullable();
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
        Schema::dropIfExists('cms_portfolio_posts');
    }
};
