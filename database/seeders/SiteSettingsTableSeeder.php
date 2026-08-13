<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SiteSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        DB::table('site_settings')->insert([
            'site_name' => "AKALIN TECH",
            'site_description' => "AKALIN TECH, dijital dünyanın güvenilir ve yenilikçi ortağıdır. Siber güvenlikten yazılım çözümlerine kadar geniş bir yelpazede ihtiyaçlarınıza özel çözümler sunuyoruz. Web siteleri, mobil uygulamalar ve daha fazlasıyla işinizi büyütmenize yardımcı oluyoruz. Teknolojinin gücünü kullanarak, işlerinizi daha verimli ve güvenli hale getiriyoruz.",
            'site_slogan' => $faker->catchPhrase,
            'site_logo' => "site_logo.png",
            'site_phone' => $faker->phoneNumber,
            'site_mail' => $faker->safeEmail,
            'site_etbis_qr' => "",
            'site_etbis_link' => "https://etbis.eticaret.gov.tr/sitedogrulama/8196FDEF645148A88D019AF721915768",
            'site_shipment_price' => 100,
            'site_instagram' => $faker->userName,
            'site_facebook' => $faker->userName,
            'site_youtube' => $faker->userName,
            'site_tiktok' => $faker->userName,
            'site_linkedin' => $faker->userName,
            'site_x' => $faker->userName,
            'site_address' => $faker->address,
            'site_google_embed_map_url'=> "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12684602.000875097!2d35.12932955000001!3d39.08764590000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b0155c964f2671%3A0x40d9dbd42a625f2a!2zVMO8cmtpeWU!5e0!3m2!1sen!2str!4v1734697109719!5m2!1sen!2str",
            'site_whatsapp_group_link' => $faker->url,
            'site_whatsapp_channel_link'=> $faker->url,
            'site_telegram_group_link'=> $faker->url,
            'site_telegram_channel_link'=> $faker->url,
            'site_facebook_group_link'=> $faker->url,
            'site_facebook_page_link'=> $faker->url,
            'site_reddit_community_link'=> $faker->url,
            'site_instagram_broadcast_channnel_link'=> $faker->url,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
