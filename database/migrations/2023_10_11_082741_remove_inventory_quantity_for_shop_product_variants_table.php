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
        Schema::table('shop_product_variant_items', function (Blueprint $table) {
            $table->dropColumn('inventory_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_product_variant_items', function (Blueprint $table) {
            $table->integer('inventory_quantity')->nullable();
        });
    }
};
