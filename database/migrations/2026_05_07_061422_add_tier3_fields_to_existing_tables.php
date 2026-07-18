<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->decimal('wholesale_price', 15, 2)->nullable();
            $table->decimal('promo_price', 15, 2)->nullable();
            $table->date('promo_start_date')->nullable();
            $table->date('promo_end_date')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            // Already has 'role' column, but we will use it for Finance, Auditor, Warehouse
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'promo_price', 'promo_start_date', 'promo_end_date']);
        });
    }
};
