<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            // Checkout saat ini guest, jadi user_id boleh null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('email', 150);
            $table->string('phone', 30);
            $table->text('address');
            $table->string('shipping_method', 20);   // standard | express
            $table->string('payment_method', 30);    // bank_transfer | qris
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            // Snapshot nama & harga agar riwayat order tidak berubah saat produk diedit/dihapus
            $table->string('product_name', 150);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('qty');
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
