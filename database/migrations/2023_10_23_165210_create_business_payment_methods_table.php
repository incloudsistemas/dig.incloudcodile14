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
        Schema::create('business_payment_methods', function (Blueprint $table) {
            $table->id();
            // Negócio
            $table->foreignId('business_id');
            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Forma de pagamento
            // 1 - Dinheiro, 2 - Pix, 3 - Cheque, 4 - Transferência bancária, 5 - Cartão de débito, 6 - Cartão de crédito, 7 - Outros...
            $table->integer('role');
            // Condições de pagamento
            // Nº de parcelas
            // 0 - à vista
            $table->integer('num_installments')->unsigned();
            // Preço
            $table->integer('price')->nullable();
            // Descrição/Observações do negócio
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
        Schema::dropIfExists('business_payment_methods');
    }
};
