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
        Schema::create('crm_funnelables', function (Blueprint $table) {
            // Funil
            $table->foreignId('funnel_id');
            $table->foreign('funnel_id')
                ->references('id')
                ->on('crm_funnels')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // funnelable_id e funnelable_type.
            $table->morphs('funnelable');

            $table->unique(['funnel_id', 'funnelable_id', 'funnelable_type'], 'funnelables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('crm_funnelables');
    }
};
