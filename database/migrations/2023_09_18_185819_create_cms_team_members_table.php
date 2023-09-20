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
        Schema::create('cms_team_members', function (Blueprint $table) {
            $table->id();
            // Título
            // (nos membros da equipe sempre será o nome do colaborador)
            $table->string('title');
            $table->string('slug')->unique();
            // Nome do colaborador
            $table->string('member_name');
            // Cargo
            $table->string('occupation')->nullable();
            // Chamada
            $table->text('excerpt')->nullable();
            // Conteúdo
            $table->longText('body')->nullable();
            // Vídeo destaque (embed)
            $table->string('embed_video')->nullable();
            // Ordem
            $table->integer('order')->unsigned()->default(1);
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
        Schema::dropIfExists('cms_team_members');
    }
};
