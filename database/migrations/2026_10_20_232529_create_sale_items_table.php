<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
#Terhubung ke nota mana
            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();
#barang apa yang dibeli
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();
#berapa banyak yang dibeli
            $table->unsignedInteger('quantity');
#harga satuan saat transaksi
            $table->decimal('unit_price', 15, 2);
#total harga
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};