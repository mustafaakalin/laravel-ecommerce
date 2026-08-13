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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->text('site_description');
            $table->string('site_slogan');
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->string('site_phone')->nullable();
            $table->string('site_mail')->nullable();
            $table->string('site_address')->nullable();
            $table->string('site_etbis_qr')->nullable();
            $table->string('site_etbis_link')->nullable();
            $table->decimal('site_shipment_price', 10, 2);
            $table->string('default_brand_image')->nullable();
            $table->string('default_category_image')->nullable();
            $table->string('default_product_image')->nullable();
            $table->string('default_testimonial_image')->nullable();
            $table->string('default_user_avatar')->nullable();
            $table->string('default_slider_image')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_instagram_broadcast_channnel')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_facebook_group')->nullable();
            $table->string('social_facebook_page')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_tiktok')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_x')->nullable();
            $table->string('social_whatsapp_group')->nullable();
            $table->string('social_whatsapp_channel')->nullable();
            $table->string('social_telegram_group')->nullable();
            $table->string('social_telegram_channel')->nullable();
            $table->string('social_reddit')->nullable();
            $table->string('social_reddit_community')->nullable();
            $table->text('google_maps_embed')->nullable();
            $table->longText('privacy_policy_text')->nullable();
            $table->longText('terms_and_conditions_text')->nullable();
            $table->longText('shipping_policy_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
