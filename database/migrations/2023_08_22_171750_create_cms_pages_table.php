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
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            // Auto relacionamento - Sub página - Ref. página pai
            $table->foreignId('page_id')->nullable()->default(null);
            $table->foreign('page_id')
                ->references('id')
                ->on('cms_pages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Título
            $table->string('title');
            $table->string('slug')->unique();
            // Subtítulo
            $table->string('subtitle')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Call to action
            $table->json('cta')->nullable();
            // Url destaque
            $table->string('url')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Permitir comentário? 1 - sim, 0 - não
            $table->boolean('comment')->default(0);
            // Configurações da página
            $table->json('settings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cms_pages');
    }
};
