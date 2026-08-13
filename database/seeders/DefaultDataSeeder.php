<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DefaultDataSeeder extends Seeder
{
    private const DEFAULT_IMAGES_PATH = 'assets/images/defaults/';
    
    public function run(): void
    {
        $this->seedDefaultSettings();
        $this->copyDefaultImages();
    }

    private function seedDefaultSettings(): void
    {
        $defaultSettings = [
            'site_name' => 'AKALIN TECH',
            'site_description' => 'AKALIN TECH E-commerce1 website , AKALIN TECH, dijital dünyanın güvenilir ve yenilikçi ortağıdır. Siber güvenlikten yazılım çözümlerine kadar geniş bir yelpazede ihtiyaçlarınıza özel çözümler sunuyoruz. Web siteleri, mobil uygulamalar ve daha fazlasıyla işinizi büyütmenize yardımcı oluyoruz. Teknolojinin gücünü kullanarak, işlerinizi daha verimli ve güvenli hale getiriyoruz.',
            'site_slogan' => 'E-commerce1 website',
            'site_phone' => '+905011609221',
            'site_mail' => 'akalintechcontact@proton.me',
            'site_address' => 'DiyarBakır, Turkey',
            'site_shipment_price' => 10.00,
            'site_logo' => self::DEFAULT_IMAGES_PATH . 'logo.png',
            'site_favicon' => self::DEFAULT_IMAGES_PATH . 'favicon.png',
            'site_etbis_qr' => self::DEFAULT_IMAGES_PATH . 'etbis_qr.png',
            'site_etbis_link' => 'https://etbis.com',
            'default_brand_image' => self::DEFAULT_IMAGES_PATH . 'brand.png',
            'default_category_image' => self::DEFAULT_IMAGES_PATH . 'category.png',
            'default_product_image' => self::DEFAULT_IMAGES_PATH . 'product.png',
            'default_testimonial_image' => self::DEFAULT_IMAGES_PATH . 'testimonial.png',
            'default_user_avatar' => self::DEFAULT_IMAGES_PATH . 'user.png',
            'default_slider_image' => self::DEFAULT_IMAGES_PATH . 'slider.png',
            'social_instagram' => 'https://www.instagram.com/akalintechs/',
            'social_facebook' => 'https://www.facebook.com/akalintech',
            'social_youtube' => 'https://www.youtube.com/@AkalinTech',
            'social_tiktok' => 'https://www.tiktok.com/@akalintech',
            'social_linkedin' => 'https://www.linkedin.com/in/akalintech',
            'social_x' => 'https://x.com/AkalinTech',
            'social_whatsapp_group' => 'https://chat.whatsapp.com/I8gYGohgjzUGX1IHKw4s17',
            'social_telegram_group' => 'https://t.me/akalintech',
            'social_reddit' => 'https://www.reddit.com/user/akalintech/',
            'google_maps_embed' => 'https://maps.google.com',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('site_settings')->insertOrIgnore($defaultSettings);
    }

    private function copyDefaultImages(): void
    {
        $sourceDir = public_path('assets/images/defaults');
        $targetDir = storage_path('app/public/assets/images/defaults');

        // Create target directory if it doesn't exist
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $defaultImages = [
            'brand.png',
            'category.png',
            'etbis_qr.png',
            'favicon.png',
            'logo.png',
            'product.png',
            'slider.png',
            'testimonial.png',
            'user.png'
        ];

        foreach ($defaultImages as $image) {
            $sourcePath = $sourceDir . '/' . $image;
            $targetPath = $targetDir . '/' . $image;

            if (File::exists($sourcePath) && !File::exists($targetPath)) {
                try {
                    File::copy($sourcePath, $targetPath);
                } catch (\Exception $e) {
                    Log::error("Failed to copy default image {$image}: " . $e->getMessage());
                }
            }
        }
    }
}
