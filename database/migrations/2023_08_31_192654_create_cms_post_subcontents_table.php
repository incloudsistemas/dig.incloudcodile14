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
        Schema::create('cms_post_subcontents', function (Blueprint $table) {
            $table->id();
            // contentable_id e contentable_type.
            $table->morphs('contentable');
            // Tipo
            // 1 - 'Abas', 2 - 'Acordeões', ...
            $table->char('role', 1)->default(1);
            // $table->enum('role', ['tabs', 'accordions']);
            // Título
            $table->string('title');
            $table->string('slug')->unique();
            // Subtítulo
            $table->string('subtitle')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Chamada para ação (Call to action)
            $table->json('cta')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Status
            // 0- Inativo, 1 - Ativo, 2 - Rascunho
            $table->char('status', 1)->default(1);
           // Atributos personalizados
           $table->json('custom')->nullable();
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
        Schema::dropIfExists('cms_post_subcontents');
    }
};
