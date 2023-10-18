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
        Schema::create('crm_contact_individuals', function (Blueprint $table) {
            $table->id();
            // Nome
            $table->string('name');
            // $table->string('slug')->unique();
            // Email
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            // Senha
            $table->string('password')->nullable();
            $table->rememberToken();
            // Email(s) adicionais
            $table->json('additional_emails')->nullable();
            // Telefone(s) de contato
            $table->json('phones')->nullable();
            // CPF
            $table->string('cpf')->nullable();
            // RG/ Órgão Expedidor
            $table->string('rg')->nullable();
            // Sexo
            // M - 'Masculino', F - 'Feminino'.
            $table->char('gender', 1)->nullable();
            // Data de nascimento
            $table->date('birth_date')->nullable();
            // Cargo
            $table->string('occupation')->nullable();
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
        Schema::dropIfExists('crm_contact_individuals');
    }
};
