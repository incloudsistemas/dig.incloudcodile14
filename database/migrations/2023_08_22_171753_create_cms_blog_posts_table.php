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
        Schema::create('cms_blog_posts', function (Blueprint $table) {
            $table->id();
            // Tipo
            // 1 - 'Artigo', 2 - 'Link', 3 - 'Galeria de Fotos', 4 - 'Vídeo'.
            $table->char('role', 1)->default(1);
            // Título
            $table->string('title');
            $table->string('slug')->unique();
            // Subtítulo
            $table->string('subtitle')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Url
            $table->string('url')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Permitir comentário? 1 - sim, 0 - não
            $table->boolean('comment')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_blog_posts');
    }
};
