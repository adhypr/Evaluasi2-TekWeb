<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id')->autoIncrement();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->foreignId('category_id');
            // Label kategori bebas (sesuai form admin Dhyecast), mis. "Hot Wheels Neon Speeders"
            $table->string('category_name', 150)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('scale', 20)->default('1:64');
            // Bukan "condition": kata itu reserved word di MySQL
            $table->string('item_condition', 30)->default('New (Mint)');
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->string('img')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();

            // Foreign key ke categories
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
