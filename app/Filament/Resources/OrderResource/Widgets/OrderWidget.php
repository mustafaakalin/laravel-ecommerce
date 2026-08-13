<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Widgets\TableWidget as BaseWidget;

class OrderWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Latest Orders';



    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }


    protected function mutateFormDataBeforeFill(array $data, $record): array
    {

        // Get existing shipment data if exists
        $shipment = $record->shipment;
        if ($shipment) {
            $data['carrier'] = $shipment->carrier;
            $data['tracking_number'] = $shipment->tracking_number;
            $data['shipped_at'] = $shipment->shipped_at;
        }
        return $data;
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->when(
                        !auth()->user()->hasRole('admin'),
                        fn($query) =>
                        $query->where('user_id', auth()->id())
                    )
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable()
                    ->columnSpanFull(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Toplam')
                    ->money('TRY')
                    ->sortable()
                    ->columnSpanFull(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'shipping' => 'Kargoda',
                        'completed' => 'Tamamlandı',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'shipping',
                        'success' => 'completed',
                    ])
                    ->columnSpanFull(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->columnSpanFull(),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                ->modalHeading('Sipariş Detayı')
                ->modalContent(fn ($record) => view('filament.resources.order-resource.order-view-modal', [
                    'order' => $record->load(['items.product', 'shipment', 'user']),
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat'),
                \Filament\Actions\EditAction::make()
                    ->form([
                        Select::make('status')
                            ->label('Sipariş Durumu')
                            ->options([
                                'pending' => 'Bekliyor',
                                'shipping' => 'Kargoda',
                                'completed' => 'Tamamlandı',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Section::make('Kargo Bilgileri')
                            ->schema([
                                Select::make('carrier')
                                    ->required()
                                    ->options([
                                        'ups' => 'UPS',
                                        'fedex' => 'FedEx',
                                        'dhl' => 'DHL',
                                        'aras' => 'Aras Kargo',
                                        'yurtici' => 'Yurtiçi Kargo',
                                        'mng' => 'MNG Kargo',
                                        'ptt' => 'PTT Kargo',
                                        'surat' => 'Sürat Kargo',
                                    ])
                                    ->searchable()
                                    ->label('Kargo Firması'),

                                TextInput::make('tracking_number')
                                    ->required()
                                    ->label('Takip Numarası')
                                    ->maxLength(255),

                                DatePicker::make('shipped_at')
                                    ->label('Kargoya Verilme Tarihi')
                                    ->default(now())
                                    ->required(),
                            ])
                            ->visible(fn(Get $get): bool => $get('status') === 'shipping')
                            ->columns(3),
                    ])
                    ->using(function ($record, array $data) {
                        $oldStatus = $record->status;
                        
                        $record->update(['status' => $data['status']]);
                
                        if ($data['status'] === 'shipping') {
                            $record->shipment()->updateOrCreate(
                                ['order_id' => $record->id],
                                [
                                    'carrier' => $data['carrier'],
                                    'tracking_number' => $data['tracking_number'],
                                    'shipped_at' => $data['shipped_at'],
                                    'status' => 'shipping'
                                ]
                            );
                        }
                
                        // Send notification if status changed
                        if ($oldStatus !== $data['status']) {
                            $record->user->notify(new \App\Notifications\OrderStatusChanged(
                                $record,
                                $oldStatus,
                                $data['status']
                            ));
                        }
                    })
                    ->visible(fn($record): bool => auth()->user()->hasRole('admin')),
            ]);
    }
}
