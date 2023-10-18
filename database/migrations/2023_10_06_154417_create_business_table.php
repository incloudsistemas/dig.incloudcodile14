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
        Schema::create('business', function (Blueprint $table) {
            $table->id();
            // Criador/Angariador "id_owner"
            $table->foreignId('user_id')->nullable()->default(null);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // Contato
            $table->foreignId('contact_id')->nullable()->default(null);
            $table->foreign('contact_id')
                ->references('id')
                ->on('crm_contacts')
                ->onUpdate('cascade')
                ->onDelete('set null');
            // Papel da negociação
            // 1 - [CRM] Padrão (Default), 1 - [Loja] Ponto de Venda (Point of Sale), 2 - [Loja] Loja Virtual
            $table->integer('role')->default(1);
            // Preço Total
            $table->integer('price')->nullable();
            // Custo Total
            $table->integer('cost')->nullable();
            // Desconto
            $table->integer('discount')->nullable();
            // Forma de pagamento
            // 1 - Dinheiro, 2 - Pix, 3 - Cheque, 4 - Transferência bancária, 5 - Cartão de débito, 6 - Cartão de crédito, 7 - Outros...
            $table->integer('payment_method')->nullable();
            // Condições de pagamento
            // Nº de parcelas
            // 0 - à vista
            $table->integer('num_installments')->unsigned()->nullable();
            // Descrição/Observações do negócio
            $table->text('description')->nullable();
            // Data do negócio
            $table->datetime('business_at')->default(now());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('business');
    }
};
