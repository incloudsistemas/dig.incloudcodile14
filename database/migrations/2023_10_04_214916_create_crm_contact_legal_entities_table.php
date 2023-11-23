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
        Schema::create('crm_contact_legal_entities', function (Blueprint $table) {
            $table->id();
            // Nome/Razão Social
            $table->string('name');
            // $table->string('slug')->unique();
            // Email
            $table->string('email')->unique()->nullable();
            // Email(s) adicionais
            $table->json('additional_emails')->nullable();
            // Telefone(s) de contato
            $table->json('phones')->nullable();
            // Nome fantasia
            $table->string('trade_name')->nullable();
            // CNPJ
            $table->string('cnpj')->nullable();
            // Inscrição municipal
            // É o número de identificação municipal da sua empresa cadastrado na prefeitura.
            $table->string('municipal_registration')->nullable();
            // Inscrição estadual
            $table->string('state_registration')->nullable();
            // Regime de tributação
            $table->char('tax_regime', 1)->nullable();
            // Url do site
            $table->string('url')->nullable();
            // Setor
            $table->string('sector')->nullable();
            //  Nº de funcionários
            $table->integer('num_employees')->nullable();
            // Receita anual
            $table->integer('anual_income')->nullable();
            // Complemento
            $table->text('complement')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_contact_legal_entities');
    }
};
