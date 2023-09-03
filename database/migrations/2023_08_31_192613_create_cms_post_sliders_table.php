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
        Schema::create('cms_post_sliders', function (Blueprint $table) {
            $table->id();
            // slideable_id e slideable_type.
            $table->morphs('slideable');
            // Tipo
            // 1 - 'Padrão (Imagem)', 2 - 'Vídeo', 3 - 'Youtube Vídeo'...
            $table->char('role', 1)->default(1);
            // $table->enum('role', ['default', 'video', 'youtube']);
            // Título
            $table->string('title');
            // Subtítulo
            $table->string('subtitle')->nullable();
            // Conteúdo
            $table->text('body')->nullable();
            // Chamada para ação (Call to action)
            $table->json('cta')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Status
            // 0- Inativo, 1 - Ativo, 2 - Rascunho
            $table->char('status', 1)->default(1);
            // Configurações do slider
            // Estilo
            // $table->enum('style', ['dark', 'light', 'none'])->default('dark');
            // Identação do texto
            // $table->enum('text_indent', ['left', 'center', 'right'])->default('left');
            $table->json('settings')->nullable();
            // Data da publicação
            $table->datetime('publish_at')->default(now());
            // Data de expiração
            $table->datetime('expiration_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_post_sliders');
    }
};
