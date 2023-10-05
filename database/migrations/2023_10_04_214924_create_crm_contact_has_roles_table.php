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
        Schema::create('crm_contact_has_roles', function (Blueprint $table) {
            // Contato
            $table->foreignId('contact_id');
            $table->foreign('contact_id')
                ->references('id')
                ->on('crm_contacts')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Tipo do contato
            $table->foreignId('contact_role_id');
            $table->foreign('contact_role_id')
                ->references('id')
                ->on('crm_contact_roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['contact_id', 'contact_role_id'], 'contact_has_roles_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('crm_contact_has_roles');
    }
};
