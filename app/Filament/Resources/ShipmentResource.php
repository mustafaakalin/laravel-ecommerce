<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\ShipmentResource\Pages;
use App\Filament\Resources\ShipmentResource\RelationManagers;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string | BackedEnum | null $navigationIcon = 'fas-truck-fast';

    protected static string | UnitEnum | null $navigationGroup = 'Sipariş İşlemleri';

    protected static ?string $navigationLabel = 'Kargoler';

    protected static ?string $modelLabel = 'Kargo';

    protected static ?string $pluralModelLabel = 'Kargoler';

    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'carrier';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Sipariş Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'id')  // Order modelinde 'number' veya benzeri bir tanımlayıcı alan
                            ->required()
                            ->searchable()
                            ->visible(fn() => auth()->user()->hasRole('admin'))
                            ->preload()
                            ->label('Sipariş')
                            ->createOptionForm([
                                // Sipariş oluşturma form alanları
                            ]),

                        Forms\Components\Select::make('carrier')
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

                        Forms\Components\TextInput::make('tracking_number')
                            ->required()
                            ->label('Takip Numarası')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Kargo takip numarasını giriniz'),
                    ])->columns(3),

                \Filament\Schemas\Components\Section::make('Durum Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Beklemede',
                                'shipped' => 'Kargoda',
                                'teslimedildi' => 'Teslim Edildi'
                            ])
                            ->default('pending')
                            ->live()
                            ->label('Durum'),

                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('Kargoya Verilme Tarihi')
                            ->timezone('Europe/Istanbul')
                            ->displayFormat('d/m/Y H:i')
                            ->visible(fn(Get $get) => in_array($get('status'), ['shipping', 'delivered']))
                            ->required(fn(Get $get) => $get('status') === 'shipping')
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Teslim Tarihi')
                            ->timezone('Europe/Istanbul')
                            ->displayFormat('d/m/Y H:i')
                            ->visible(fn(Get $get) => $get('status') === 'delivered')
                            ->required(fn(Get $get) => $get('status') === 'delivered')
                            ->default(now()),
                    ])->columns(3),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Sipariş No')
                    ->sortable()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Takip numarası kopyalandı')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('carrier')
                    ->label('Kargo Firması')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'shipped' => 'info',
                        'teslimedildi' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'shipped' => 'Kargoda',
                        'teslimedildi' => 'Teslim Edildi',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('Kargoya Verilme')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Teslim Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Beklemede',
                        'shipping' => 'Kargoda',
                        'delivered' => 'Teslim Edildi'
                    ])
                    ->label('Durum'),

                Tables\Filters\SelectFilter::make('carrier')
                    ->options([
                        'ups' => 'UPS',
                        'fedex' => 'FedEx',
                        'dhl' => 'DHL',
                        'aras' => 'Aras Kargo',
                        'yurtici' => 'Yurtiçi Kargo',
                        'mng' => 'MNG Kargo',
                        'ptt' => 'PTT Kargo',
                    ])
                    ->label('Kargo Firması'),

                Tables\Filters\Filter::make('shipped_at')
                    ->form([
                        Forms\Components\DatePicker::make('shipped_from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('shipped_until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['shipped_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('shipped_at', '>=', $date),
                            )
                            ->when(
                                $data['shipped_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('shipped_at', '<=', $date),
                            );
                    })
                    ->label('Kargoya Verilme Tarihi'),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make(),
                    \Filament\Actions\EditAction::make(),
                    \Filament\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }






    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('admin')) {
            // Admin için tüm kargoların sayısı
            return static::getModel()::count();
        }

        // Normal kullanıcı için sadece kendi kargolarının sayısı
        return static::getModel()::whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admin değilse sadece kendi siparişlerine ait kargoları göster
        if (!auth()->user()->hasRole('admin')) {
            $query->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'view' => Pages\ViewShipment::route('/{record}'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
