<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Models\Order;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;



    public function generateInvoice(Order $order)
    {
        // Configure PDF options
        $defaultConfig = (new \Dompdf\Dompdf())->getOptions();
        $defaultConfig->set('defaultFont', 'DejaVu Sans');
        $defaultConfig->set('isRemoteEnabled', true);
        $defaultConfig->set('isHtml5ParserEnabled', true);

        // UTF-8 encoding for customer data
        $customerName = mb_convert_encoding($order->user->name, 'UTF-8', 'auto');


        // Site sahibi bilgileri config/invoices.php'den alınır
        $customer = new Buyer([
            'name' => htmlspecialchars($order->user->name, ENT_QUOTES, 'UTF-8'),
            'custom_fields' => [
                'email' => $order->user->email,
                'phone' => $order->user->phone ?? '',
                'address' => htmlspecialchars($order->address->address ?? '', ENT_QUOTES, 'UTF-8'),
            ],
        ]);

        $items = [];
        foreach ($order->items as $item) {
            $items[] = (new InvoiceItem())
                ->title(mb_convert_encoding($item->product->name, 'UTF-8', 'auto'))
                ->pricePerUnit($item->price)
                ->quantity($item->quantity);
        }

        $invoice = Invoice::make()
            ->buyer($customer)
            ->sequence($order->id) // Fatura numarası olarak sipariş ID'si
            ->serialNumberFormat('{SEQUENCE}')
            ->currencySymbol('₺')
            ->currencyCode('TRY')
            ->dateFormat('d/m/Y')
            ->payUntilDays(7) // Ödeme vadesi
            ->filename("invoice_{$order->id}") // PDF dosya adı
            ->addItems($items);

        // Varsa indirimler
        if ($order->discount > 0) {
            $invoice->totalDiscount($order->discount);
        }

        // Kargo ücreti
        // if ($order->shipping_cost > 0) {
        //     $invoice->shipping($order->shipping_cost);
        // }

        // return $invoice->stream(); // PDF olarak görüntüle
        // Alternatif olarak:
        // return $invoice->download(); // PDF'i indir
        // $invoice->save('storage/invoices'); // Depolama alanına kaydet


        // UTF-8 headers for PDF response
        return $invoice->stream()
            // ->header('Content-Type', 'application/pdf; charset=UTF-8');

            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="fatura.pdf"')
            ->header('Accept-Charset', 'UTF-8');

    }


    // public function generateInvoice(Order $order)
    // {
    //     // Customer data encoding
    //     $customer = new Buyer([
    //         'name' => html_entity_decode(utf8_encode($order->user->name)),
    //         'custom_fields' => [
    //             'email' => $order->user->email,
    //             'phone' => $order->user->phone ?? '',
    //             'address' => html_entity_decode(utf8_encode($order->address->full_address ?? '')),
    //         ],
    //     ]);

    //     $items = [];
    //     foreach ($order->items as $item) {
    //         $items[] = (new InvoiceItem())
    //             ->title(html_entity_decode(utf8_encode($item->product->name)))
    //             ->pricePerUnit($item->price)
    //             ->quantity($item->quantity);
    //     }

    //     $invoice = Invoice::make('FATURA')
    //         ->buyer($customer)
    //         ->sequence($order->id)
    //         ->serialNumberFormat('{SEQUENCE}')
    //         ->currencySymbol('₺')
    //         ->currencyCode('TRY')
    //         ->dateFormat('d/m/Y')
    //         ->payUntilDays(7)
    //         ->filename("fatura_{$order->id}")
    //         ->addItems($items);

    //     if ($order->discount > 0) {
    //         $invoice->totalDiscount($order->discount);
    //     }

    //     if ($order->shipping_cost > 0) {
    //         $invoice->shipping($order->shipping_cost);
    //     }

    //     $pdf = $invoice->stream();

    //     return response($pdf)
    //         ->header('Content-Type', 'application/pdf')
    //         ->header('Content-Disposition', 'inline; filename="fatura.pdf"')
    //         ->header('Accept-Charset', 'UTF-8');
    // }




    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('generate_invoice')
                ->label('Fatura Oluştur')
                ->action(function () {
                    return $this->generateInvoice($this->record);
                })
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
