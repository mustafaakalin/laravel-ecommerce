<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_description',
        'site_slogan',
        'site_logo',
        'site_favicon',
        'site_phone',
        'site_mail',
        'site_address',
        'site_etbis_qr',
        'site_etbis_link',
        'site_shipment_price',
        'default_brand_image',
        'default_category_image',
        'default_product_image',
        'default_testimonial_image',
        'default_user_avatar',
        'default_slider_image',
        'social_instagram',
        'social_facebook',
        'social_youtube',
        'social_tiktok',
        'social_linkedin',
        'social_x',
        'social_whatsapp_group',
        'social_telegram_group',
        'social_reddit',
        'google_maps_embed',
        'privacy_policy_text',
        'terms_and_conditions_text',
        'shipping_policy_text',
    ];
}
