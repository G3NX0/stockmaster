<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $header) {
            $header->id();
            $header->foreignId('item_id')->constrained()->cascadeOnDelete();
            $header->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $header->string('batch_number');
            $header->decimal('quantity', 15, 2)->default(0);
            $header->date('expiry_date')->nullable();
            $header->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
