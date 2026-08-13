<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Order $order,
        protected string $oldStatus,
        protected string $newStatus)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }



    public function toMail(): MailMessage
    {
        $statusMap = [
            'pending' => 'Bekliyor',
            'shipping' => 'Kargoda',
            'completed' => 'Tamamlandı'
        ];

        return (new MailMessage)
            ->subject('Sipariş Durumu Güncellendi - #' . $this->order->id)
            ->greeting('Merhaba ' . $this->order->user->name)
            ->line('Siparişinizin durumu güncellendi.')
            ->line("Eski Durum: " . ($statusMap[$this->oldStatus] ?? $this->oldStatus))
            ->line("Yeni Durum: " . ($statusMap[$this->newStatus] ?? $this->newStatus))
            ->when($this->newStatus === 'shipping', function($message) {
                return $message
                    ->line("Kargo Firması: " . $this->order->shipment->carrier)
                    ->line("Takip Numarası: " . $this->order->shipment->tracking_number);
            })
            ->action('Siparişi Görüntüle', route('filament.admin.resources.orders.view', $this->order));
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
