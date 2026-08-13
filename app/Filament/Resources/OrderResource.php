<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | UnitEnum | null $navigationGroup = 'Sipariş İşlemleri';

    protected static ?string $navigationLabel = 'Siparişler';

    protected static ?string $modelLabel = 'Sipariş';

    protected static ?string $pluralModelLabel = 'Siparişler';

    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'status';



    // Badge rengi - bekleyen siparişler varsa turuncu
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->exists()
            ? 'warning'
            : 'success';
    }





    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Sipariş Detayları')
                    ->description('Temel sipariş bilgileri')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
    
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Bekliyor',
                                'shipping' => 'Yolda (Kargo)',
                                'delivered' => 'Tamamlandı',
                            ])
                            ->default('Bekliyor')
                            ->required()
                            ->columnSpan(1),
                    ]),
    
                Section::make('Sipariş Ürünleri')
                    ->description('Siparişteki ürünler ve detayları')
                    ->icon('heroicon-o-shopping-cart')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Ürün Adı')
                                            ->relationship('product', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                                if ($product = \App\Models\Product::find($state)) {
                                                    $set('price', $product->price);
                                                }
                                            }),
    
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Miktar')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set, \Filament\Schemas\Components\Utilities\Get $get) {
                                                $price = $get('price');
                                                if ($price) {
                                                    $set('subtotal', $price * $state);
                                                }
                                            }),
    
                                        Forms\Components\TextInput::make('price')
                                            ->label('Fiyat')
                                            ->prefix('₺')
                                            ->numeric()
                                            ->required()
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => 
                                \App\Models\Product::find($state['product_id'])?->name ?? 'Yeni Ürün'
                            )
                            ->defaultItems(1)
                            ->required(),
                    ]),
    
                Section::make('Sipariş Özeti')
                    ->description('Toplam tutar ve son detaylar')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\TextInput::make('total_price')
                            ->label('Toplam Fiyat')
                            ->prefix('₺')
                            ->numeric()
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->sortable()
                    ->searchable(), // Kullanıcı adını göstermek için ilişki üzerinden alınır

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Toplam Fiyat')
                    ->money('TRY') // Para birimi ekler
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Bekliyor',
                        'shipping' => 'Yolda (Kargo)',
                        'delivered' => 'Tamamlandı',
                        default => ucfirst($state),
                    }) // Durumları renklendirerek gösterir
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'shipping',
                        'success' => 'delivered',
                    ]), // Durum alanını renkli etiketlerle gösterir

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Bekliyor',
                        'shipping' => 'Yolda (Kargo)',
                        'delivered' => 'Tamamlandı',
                    ])
                    ->label('Duruma Göre Filtrele'), // Durumlara göre filtre ekler
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make()
                
                ->visible(fn() => auth()->user()->hasRole('admin')),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            RelationManagers\ShipmentRelationManager::class,
        ];
    }




    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admin kullanıcısı için filtreleme yapmıyoruz, tüm kayıtları görebilmeli
        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }


    public static function getNavigationBadge(): ?string
    {
        if (!auth()->user()->hasRole('admin')) {
            // Normal kullanıcı için sadece kendi Like sayısı
            return Order::where('user_id', auth()->id())->count();
        }

        // Admin için tüm Like sayısı
        return Order::count();
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    
}
