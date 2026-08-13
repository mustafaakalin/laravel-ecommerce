<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FaqTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faqs = [
            ['question' => 'Siparişimi nasıl takip edebilirim?', 'answer' => 'Siparişinizi takip etmek için:\n\n1. Hesabım sayfasına giriş yapın\n2. Siparişlerim bölümüne gidin\n3. İlgili siparişin detaylarını görüntüleyin\n4. Kargo takip numarasını kullanarak kargo firmasının sitesinden takip edebilirsiniz'],
            ['question' => 'İade ve değişim politikanız nedir?', 'answer' => 'Ürünler 14 gün içinde iade edilebilir\n* Ürün kullanılmamış ve orijinal ambalajında olmalıdır\n* İade kargo ücreti alıcıya aittir\n* Para iadesi 3-5 iş günü içinde yapılır\n* Değişim için lütfen müşteri hizmetlerimizle iletişime geçin'],
            ['question' => 'Ürünlerinizin garantisi var mı?', 'answer' => 'Evet, ürünlerimiz 2 yıl garantilidir. Garanti kapsamında ücretsiz onarım veya değişim hizmeti sunulmaktadır.'],
            ['question' => 'Kargo ücreti ne kadar?', 'answer' => 'Kargo ücreti 9.90 TL\'dir. 150 TL ve üzeri alışverişlerde kargo ücretsizdir.'],
            ['question' => 'Kredi kartı bilgilerim güvende mi?', 'answer' => 'Evet, kredi kartı bilgileriniz 256 bit SSL sertifikası ile korunmaktadır.'],
            ['question' => 'Siparişimi iptal edebilir miyim?', 'answer' => 'Siparişinizi kargoya verilmeden iptal edebilirsiniz. Siparişiniz kargoya verildikten sonra iptal edemezsiniz.'],
            ['question' => 'Ürünleriniz orijinal mi?', 'answer' => 'Evet, tüm ürünlerimiz orijinaldir.'],
            ['question' => 'Ürünlerinizin fiyatları neden bu kadar uygun?', 'answer' => 'Ürünlerimizi uygun fiyatlarla sunmamızın sebebi, ürünleri doğrudan tedarikçiden satın almamızdır. Bu sayede aracı maliyetlerini ortadan kaldırarak müşterilerimize en uygun fiyatları sunuyoruz.'],
            ['question' => 'Siparişimi nasıl değiştirebilirim?', 'answer' => 'Siparişinizi değiştirmek için:\n\n1. Hesabım sayfasına giriş yapın\n2. Siparişlerim bölümüne gidin\n3. İlgili siparişin detaylarını görüntüleyin\n4. Değişim talebinde bulunun'],
            ['question' => 'Ürünlerinizin kalitesi nasıl?', 'answer' => 'Tüm ürünlerimiz yüksek kalite standartlarına uygun olarak üretilmektedir.'],
            ['question' => 'Ürünlerinizin iadesi nasıl yapılır?', 'answer' => 'Ürün iadesi için müşteri hizmetlerimizle iletişime geçebilirsiniz.'],
            ['question' => 'Ürünlerinizin iadesi ne kadar sürer?', 'answer' => 'Ürün iadesi 3-5 iş günü içinde gerçekleştirilir.'],
        ];


        foreach ($faqs as $faq) {
            DB::table('faqs')->insert([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
