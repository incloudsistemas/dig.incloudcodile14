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
        Schema::create('cms_posts', function (Blueprint $table) {
            $table->id();
            // postable_id e postable_type.
            $table->morphs('postable');
            // Criador/Angariador "id_owner"
            $table->foreignId('user_id')->nullable()->default(null);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            // Em destaque? 1 - sim, 0 - não
            $table->boolean('featured')->default(0);
            // Ordem
            $table->integer('order')->unsigned()->default(1);
            // Status
            // 0- Inativo, 1 - Ativo, 2 - Rascunho
            // (depende do postable)
            $table->char('status', 1)->default(1);
            // Atributos personalizados
            $table->json('custom')->nullable();
            // Data da publicação
            $table->timestamp('publish_at')->default(date('Y-m-d H:i:s'));
            // Data de expiração
            $table->timestamp('expiration_at')->nullable();
            $table->timestamps();
            $table->softDeletes();            
            // Permite apenas um post por registro.
            $table->unique(['postable_id', 'postable_type'], 'postable_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cms_posts');
    }
};
