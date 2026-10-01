<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'products_active_created_idx');
            $table->index(['is_active', 'is_featured', 'stock', 'created_at'], 'products_featured_idx');
            $table->index(['category_id', 'is_active', 'stock', 'created_at'], 'products_category_active_idx');
            $table->index(['brand_id', 'is_active', 'stock', 'created_at'], 'products_brand_active_idx');
            $table->index(['is_active', 'view_count'], 'products_active_views_idx');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id', 'carts_user_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_idx');
            $table->index('product_id', 'cart_items_product_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
            $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_idx');
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
            $table->index('payment_id', 'orders_payment_id_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['product_id', 'order_id'], 'order_items_product_order_idx');
            $table->index(['order_id', 'product_id'], 'order_items_order_product_idx');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->index('product_id', 'likes_product_idx');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex('likes_product_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['product_id', 'created_at'], 'comments_product_created_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['parent_id', 'is_active'], 'categories_parent_active_idx');
        });

        Schema::table('product_ratings', function (Blueprint $table) {
            $table->index(['product_id', 'user_id'], 'product_ratings_product_user_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_product_idx');
            $table->dropIndex('cart_items_cart_product_idx');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_created_idx');
            $table->dropIndex('products_featured_idx');
            $table->dropIndex('products_category_active_idx');
            $table->dropIndex('products_brand_active_idx');
            $table->dropIndex('products_active_views_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_idx');
            $table->dropIndex('orders_user_status_created_idx');
            $table->dropIndex('orders_status_created_idx');
            $table->dropIndex('orders_payment_id_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_order_idx');
            $table->dropIndex('order_items_order_product_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_product_created_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_parent_active_idx');
        });

        Schema::table('product_ratings', function (Blueprint $table) {
            $table->dropIndex('product_ratings_product_user_idx');
        });
    }
};
