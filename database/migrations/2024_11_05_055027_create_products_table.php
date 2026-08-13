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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(1);
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->decimal('old_price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->nullable();
            $table->boolean('is_new')->default(true)->nullable();
            $table->integer('discount')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('search_keywords')->nullable();
            $table->boolean('is_digital')->nullable();
            $table->integer('view_count')->default(0);
            $table->json('specifications')->nullable();
            $table->string('sku')->unique();
            $table->boolean('is_free_shipping')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
