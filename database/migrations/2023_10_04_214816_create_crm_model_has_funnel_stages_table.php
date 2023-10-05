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
        Schema::create('crm_model_has_funnel_stages', function (Blueprint $table) {
            $table->id();
            // Funil
            $table->foreignId('funnel_id');
            $table->foreign('funnel_id')
                ->references('id')
                ->on('crm_funnels')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // Estágio do funil
            $table->foreignId('funnel_stage_id');
            $table->foreign('funnel_stage_id')
                ->references('id')
                ->on('crm_funnel_stages')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            // model_id e model_type.
            $table->morphs('model');
            $table->timestamps();

            $table->unique(['funnel_id', 'model_id', 'model_type'], 'model_has_funnel_stages_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('crm_model_has_funnel_stages');
    }
};
