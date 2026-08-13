<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use App\Models\Cart;
use Filament\Tables;
use App\Models\Product;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\CartResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CartResource\RelationManagers;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string | UnitEnum | null $navigationGroup = 'Sepet Yönetimi';

    protected static ?string $navigationLabel = 'Sepetler';

    protected static ?string $modelLabel = 'Sepet';

    protected static ?string $pluralModelLabel = 'Sepetler';

    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'items.product_id';



    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Section::make('Sepet Bilgileri')
                    ->schema([
                        // User seçimi sadece admin için görünür olmalı
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Müşteri')
                            ->columnSpan(2)
                            ->visible(fn() => auth()->user()->hasRole('admin'))
                            ->default(fn() => auth()->user()->hasRole('admin') ? null : auth()->id())
                            ->disabled(),

                        // Toplam ürün adedi göstergesi
                        Placeholder::make('items_count')
                            ->label('Toplam Ürün Adedi')
                            ->content(fn($record) => $record ? $record->calculateTotalItems() : '0')
                            ->columnSpan(1),

                        // Toplam tutar göstergesi
                        Placeholder::make('total_price')
                            ->label('Toplam Tutar')
                            ->content(fn($record) => $record ? number_format($record->calculateTotalPrice(), 2) . ' TL' : '0.00 TL')
                            ->columnSpan(1),
                    ])->columns(4),

                Section::make('Sepet Ürünleri')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn() => !auth()->user()->hasRole('admin'))
                                    ->required()
                                    ->label('Ürün')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            $set('current_price', $product ? $product->price : 0);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(fn (Get $get) => $get('product_id') ? Product::find($get('product_id'))?->stock : null)
                                    ->default(1)
                                    ->required()
                                    ->label('Adet')
                                    ->rules(['required', 'integer', 'min:1']),

                                Forms\Components\TextInput::make('current_price')
                                    ->disabled()
                                    ->numeric()
                                    ->label('Birim Fiyat')
                                    ->formatStateUsing(fn($state) => number_format($state, 2))
                                    ->suffixIcon('heroicon-m-currency-bangladeshi'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Ürün Ekle')
                            ->deleteAction(
                                fn(Forms\Components\Actions\Action $action) =>
                                $action->requiresConfirmation()
                            )
                    ])->collapsible(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kullanıcı kolonu sadece admin için görünür olsun
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('items.product_id')
                    ->label('Ürün Adedi')
                    ->alignCenter()
                    ->getStateUsing(fn(Cart $record) => $record->calculateTotalItems())
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Toplam'),
                    ]),

                    Tables\Columns\TextColumn::make('items.id')
                    ->money('TRY')
                    ->label('Toplam Tutar')
                    ->getStateUsing(fn(Cart $record) => $record->calculateTotalPrice())
                    ->alignRight()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('TRY')
                            ->label('Genel Toplam')
                            // Sadece admin için özet göster
                            ->visible(fn () => auth()->user()->hasRole('admin'))
                    ])
                    // Admin olmayan kullanıcılar için filtreleme yap
                    ->formatStateUsing(function ($state, Cart $record) {
                        if (!auth()->user()->hasRole('admin') && $record->user_id !== auth()->id()) {
                            return null;
                        }
                        return number_format($state, 2);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->label('Oluşturulma Tarihi'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->preload()
                    ->label('Müşteri'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Başlangıç: ' . Carbon::parse($data['created_from'])->format('d.m.Y');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Bitiş: ' . Carbon::parse($data['created_until'])->format('d.m.Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),

                \Filament\Actions\EditAction::make(),

                \Filament\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

    // Aktif sepet sayısını göster
    public static function getNavigationBadge(): ?string
{
    if (!auth()->user()->hasRole('admin')) {
        // Normal kullanıcı için sadece kendi sepetlerinin sayısı
        return Cart::where('user_id', auth()->id())->count();
    }
    
    // Admin için tüm sepetlerin sayısı
    return Cart::count();
}

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    // Form verileri kaydedilmeden önce user_id'yi ayarlamak için
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!auth()->user()->hasRole('admin')) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    // Sepet detaylarını göstermek için
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // Widget'lar ekleyelim
    public static function getWidgets(): array
    {
        return [
            //
        ];
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'view' => Pages\ViewCart::route('/{record}'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }
}
