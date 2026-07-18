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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('selling_price', 15, 2)->nullable()->after('harga_barang');
            $table->boolean('is_asset')->default(false)->after('min_stock');
            $table->date('purchase_date')->nullable()->after('is_asset');
            $table->integer('useful_life_months')->nullable()->after('purchase_date');
            $table->decimal('salvage_value', 15, 2)->nullable()->after('useful_life_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['selling_price', 'is_asset', 'purchase_date', 'useful_life_months', 'salvage_value']);
        });
    }
};
