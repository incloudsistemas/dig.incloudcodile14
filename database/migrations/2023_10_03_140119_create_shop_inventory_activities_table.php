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
        Schema::create('shop_inventory_activities', function (Blueprint $table) {
            $table->id();
            // Inventário
            $table->foreignId('inventory_id');
            $table->foreign('inventory_id')
                ->references('id')
                ->on('shop_product_inventories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Criador/Angariador "id_owner"
            $table->foreignId('user_id')->nullable()->default(null);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // Alterado de:
            $table->json('changed_from')->nullable();
            // Alterado para
            $table->json('changed_to')->nullable();
            // Descrição/Razão
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('shop_inventory_activities');
    }
};
