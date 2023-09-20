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
        Schema::create('cms_external_useful_links', function (Blueprint $table) {
            $table->id();
            // Título
            $table->string('title');
            $table->string('slug')->unique();
            // Chamada
            $table->text('excerpt')->nullable();
            // Url
            $table->string('url')->nullable();
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
        Schema::dropIfExists('cms_external_useful_links');
    }
};
