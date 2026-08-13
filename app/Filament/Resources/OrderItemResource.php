<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;



class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string | UnitEnum | null $navigationGroup = 'Sipariş İşlemleri';
    
    protected static ?string $navigationLabel = 'Sipariş Detayları';
    
    protected static ?string $modelLabel = 'Sipariş Detayı';
    
    protected static ?string $pluralModelLabel = 'Sipariş Detayları';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'quantity';
    

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('order_id')
                    ->label('Sipariş')
                    ->relationship('order', 'id') // order ilişkisindeki id alanını kullanarak seçim yapmayı sağlar
                    ->required()
                    ->searchable()
                    ->placeholder('Sipariş Seçiniz')
                    ->hint('Mevcut bir sipariş seçin'),

                Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name') // product ilişkisindeki name alanını kullanarak seçim yapmayı sağlar
                    ->required()
                    ->searchable()
                    ->placeholder('Ürün Seçiniz')
                    ->hint('Listeden bir ürün seçin'),

                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->label('Miktar')
                    ->helperText('Ürünün miktarını belirtin'),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->label('Fiyat')
                    ->helperText('Ürünün birim fiyatını girin'),

                TextInput::make('total_price')
                    ->label('Toplam Fiyat')
                    ->default(fn(callable $get) => $get('quantity') * $get('price'))
                    ->prefix('$')
                    ->disabled() // kullanıcıların bu alanı düzenlemesini önler
                    ->helperText('Toplam otomatik olarak hesaplanır'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->label('Sipariş No')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->tooltip('İlgili sipariş kimliği IDsi'),

                TextColumn::make('product.name')
                    ->label('Ürün Adı')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->tooltip('Bu sipariş kalemiyle ilişkili ürün'),

                TextColumn::make('quantity')
                    ->numeric()
                    ->label('Ürün miktarı')
                    ->sortable()
                    ->toggleable()
                    ->tooltip('Ürün miktarı'),

                TextColumn::make('price')
                    ->money('TRY', true) // 'TRY' yerine varsayılan para birimini kullanabilirsiniz
                    ->sortable()
                    ->label('Birim fiyatı')
                    ->tooltip('Ürünün birim fiyatı'),

                TextColumn::make('total_price')
                    ->label('Toplam Fiyat')
                    ->getStateUsing(fn($record) => $record->getTotalPrice())
                    ->money('TRY', true)
                    ->tooltip('Otomatik olarak hesaplanan toplam fiyat'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('recent')
                    ->label('Son Eklenenler')
                    ->query(fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }




    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    // Opsiyonel: Günlük sipariş ürün sayısını badge olarak göster
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'view' => Pages\ViewOrderItem::route('/{record}'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
