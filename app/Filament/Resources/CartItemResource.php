<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\CartItemResource\Pages;
use App\Filament\Resources\CartItemResource\RelationManagers;
use App\Filament\Resources\CartItemResource\Widgets\CartItemWidget;
use App\Models\CartItem;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\RawJs;

class CartItemResource extends Resource
{
    protected static ?string $model = CartItem::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-gift';

    protected static string | UnitEnum | null $navigationGroup = 'Sepet Yönetimi';
    
    protected static ?string $navigationLabel = 'Sepet Ürünleri';
    
    protected static ?string $modelLabel = 'Sepet Ürünü';
    
    protected static ?string $pluralModelLabel = 'Sepet Ürünleri';
    
    protected static ?int $navigationSort = 2;


    
    protected static ?string $recordTitleAttribute = 'product.name';
    
    // Günlük toplam sepet ürün sayısı
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Sepet Ürün Detayı')
                    ->description('Sepete eklenecek ürün bilgilerini düzenleyin')
                    ->schema([
                        Forms\Components\Select::make('cart_id')
                            ->relationship('cart')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Sepet #{$record->id} - {$record->user->name}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Sepet')
                            ->columnSpan(2),
    
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Ürün')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if ($productId = $get('product_id')) {
                                    $product = \App\Models\Product::find($productId);
                                    $quantity = $get('quantity') ?: 1;
                                    
                                    $set('unit_price', $product?->$product->price ?? 0);
                                    $set('total_price', ($product?->getCurrentPrice() ?? 0) * $quantity);
                                }
                            })
                            ->columnSpan(2),
    
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->label('Adet')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if ($productId = $get('product_id')) {
                                    $product = \App\Models\Product::find($productId);
                                    $quantity = $get('quantity') ?: 1;
                                    $set('total_price', ($product?->getCurrentPrice() ?? 0) * $quantity);
                                }
                            })
                            ->columnSpan(1),
    
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Birim Fiyat')
                            ->disabled()
                            ->readOnly()
                            ->numeric()
                            ->prefix('₺')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->dehydrated(false)
                            ->columnSpan(1),
    
                        Forms\Components\TextInput::make('total_price')
                            ->label('Toplam Tutar')
                            ->disabled()
                            ->readOnly()
                            ->numeric()
                            ->prefix('₺')
                            ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2))
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(7)
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cart.user.name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => "Sepet #{$record->cart_id}"),
    
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->product->getCurrentPrice() . ' TL'),
    
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Adet')
                    ->alignment('center')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Toplam Adet')
                    ]),
    
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Toplam Tutar')
                    ->money('TRY')
                    ->alignment('right')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Genel Toplam')
                            ->formatStateUsing(fn ($state) => number_format($state, 2) . ' TL')
                    ]),
    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Eklenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cart')
                    ->relationship('cart.user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Müşteriye Göre'),
    
                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Ürüne Göre'),
    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, 
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, 
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->modalWidth('lg'),
                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->poll('10s');
    }




    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCartItems::route('/'),
            'create' => Pages\CreateCartItem::route('/create'),
            'view' => Pages\ViewCartItem::route('/{record}'),
            'edit' => Pages\EditCartItem::route('/{record}/edit'),
        ];
    }


    // Widget'lar ekleyelim
    public static function getWidgets(): array
    {
        return [
            CartItemWidget::class,
        ];
    }
}
