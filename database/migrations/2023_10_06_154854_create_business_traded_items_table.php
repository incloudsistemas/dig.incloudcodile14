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
        Schema::create('business_traded_items', function (Blueprint $table) {
            $table->id();
            // Negócio
            $table->foreignId('business_id');
            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // businessable_id e businessable_type.
            $table->morphs('businessable');
            // Quantidade
            $table->integer('quantity')->unsigned()->default(1);
            // Preço total
            $table->integer('price')->nullable();
            // Preço unitário
            $table->integer('unit_price')->nullable();
            // Custo total
            $table->integer('cost')->nullable();
            // Custo unitário
            $table->integer('unit_cost')->nullable();
            // Descrição/Observações do item
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
        Schema::dropIfExists('business_traded_items');
    }
};
