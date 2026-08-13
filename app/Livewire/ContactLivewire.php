<?php

namespace App\Livewire;

use GuzzleHttp\Client;
use App\Models\Contact;
use Livewire\Component;
use App\Models\SiteSetting;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Log;

class ContactLivewire extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';

    public $settings;
    public $success = false;

    public function mount()
    {
        $this->settings = SiteSetting::first();
    }

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:50|regex:/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]{3,50}$/',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^5[0-9]{9}$/'], // Turkish mobile number format
            'message' => 'required|min:10|max:1000',
        ];
    }

    protected $messages = [
        'name.required' => 'Ad Soyad alanı zorunludur.',
        'name.min' => 'Ad Soyad en az 3 karakter olmalıdır.',
        'name.max' => 'Ad Soyad en fazla 50 karakter olmalıdır.',
        'name.regex' => 'Ad Soyad sadece harf içermelidir.',
        'email.required' => 'E-posta alanı zorunludur.',
        'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        'phone.required' => 'Telefon alanı zorunludur.',
        'phone.regex' => 'Geçerli bir telefon numarası giriniz. Örnek: 5XX XXX XX XX',
        'message.required' => 'Mesaj alanı zorunludur.',
        'message.min' => 'Mesaj en az 10 karakter olmalıdır.',
        'message.max' => 'Mesaj en fazla 1000 karakter olmalıdır.',
    ];

    private function sendTelegramMessage($message)
    {
        $telegramBotToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $client = new Client();

        try {
            $client->post("https://api.telegram.org/bot{$telegramBotToken}/sendMessage", [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML', // Optional: For formatting
                ],
            ]);
        } catch (\Exception $e) {
            // Handle exceptions if needed
            Log::error("Telegram API error: " . $e->getMessage());
        }
    }

    public function submit()
    {
        try {
            $validated = $this->validate();

            // Store form data before reset
            $name = $this->name;
            $email = $this->email;
            $phone = $this->phone;
            $message = $this->message;

            // Create contact record
            Contact::create($validated);

            $this->dispatch('showToast', type: 'success', message: 'Mesajınız başarıyla gönderildi!');

            // Send Telegram notification with stored data
            $telegramMessage = sprintf(
                "%s!\nYeni İletişim Mesajı alındı!\nİsim: %s\nE-posta: %s\nTelefon: %s\n\nMesaj:\n%s\n\nDurum: İletişime Geçildi.",
                config('app.name'),
                $name,
                $email,
                $phone,
                $message
            );

            $this->sendTelegramMessage($telegramMessage);

            // Reset form and show success
            $this->reset(['name', 'email', 'phone', 'message']);
            $this->success = true;
            $this->dispatch('showToast', type: 'success', message: 'Mesajınız başarıyla gönderildi!');
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            $this->dispatch('showToast', type: 'error', message: 'Bir hata oluştu, lütfen tekrar deneyin.');
        }
    }

    public function render()
    {
        return view('livewire.contact-livewire');
    }
}
