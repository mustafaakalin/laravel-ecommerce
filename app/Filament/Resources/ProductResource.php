<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Models\Tag;
use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Services\OpenAiService;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Services\HuggingFaceService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | UnitEnum | null $navigationGroup = 'Ürün Yönetimi';

    protected static ?string $navigationLabel = 'Ürünler';

    protected static ?string $modelLabel = 'Ürün';

    protected static ?string $pluralModelLabel = 'Ürünler';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'info';
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(2)->schema([
                    // Ürün Temel Bilgileri
                    \Filament\Schemas\Components\Section::make('Ürün Detayları')
                        ->description('Ürün hakkında temel bilgiler')
                        ->icon('heroicon-o-shopping-bag')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Ürün Adı')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set) {
                                    $set('slug', Str::slug($state));
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->maxLength(255)
                                ->readOnly()  // Otomatik oluşturulması için devre dışı
                                ->helperText('Ürün adıyla otomatik olarak oluşturulacaktır.'),

                            Forms\Components\RichEditor::make('description')
                                ->label('Açıklama')
                                ->required()
                            ->helperText('Ürün açıklamasından Yapay Zeka ile etiketleri çıkarır ve ekler.')
                                ->columnSpanFull(),
                            \Filament\Schemas\Components\Actions::make([
                                \Filament\Actions\Action::make('Etiketleri YZ ile Çıkar')
                                    ->label('Etiket Çıkar')
                                    ->action(function (\Filament\Schemas\Components\Utilities\Get $get, \Filament\Schemas\Components\Utilities\Set $set) {
                                        $huggingFaceService = new HuggingFaceService();
                                        try {
                                            // Generate tags using HuggingFaceService
                                            $tagsHF = $huggingFaceService->generateTags($get('description'));

                                            // Ensure uniqueness when merging with existing tags
                                            $existingTags = $get('tags') ?? [];
                                            $allTags = array_unique(array_merge($existingTags, $tagsHF));

                                            $set('tags', $allTags);
                                        } catch (\Exception $e) {
                                            Log::error('ProductResource: Tag extraction failed.', ['message' => $e->getMessage()]);
                                            Session::flash('error', 'Tag extraction failed. Please try again later.');
                                        }
                                    })
                                    ->button() // Ensure it's rendered as a button
                            ])

                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    // Fiyatlandırma ve Stok Bilgileri
                    \Filament\Schemas\Components\Section::make('Fiyatlandırma ve Stok Bilgileri')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->label('Fiyat')
                                ->required()
                                ->numeric()
                                ->prefix('$')
                                ->helperText('Ürün fiyatını belirleyin.'),

                            Forms\Components\TextInput::make('old_price')
                                ->label('Eski Fiyat')
                                ->numeric()
                                ->prefix('$')
                                ->helperText('İndirim yapılmış ürünler için eski fiyat.'),

                            Forms\Components\TextInput::make('discount')
                                ->label('İndirim (%)')
                                ->numeric()
                                ->default(0)
                                ->helperText('İndirim yüzdesini girin.'),

                            Forms\Components\TextInput::make('stock')
                                ->label('Stok')
                                ->numeric()
                                ->default(1)
                                ->helperText('Mevcut stok miktarını girin.'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    // Kategori ve Marka Bilgileri
                    \Filament\Schemas\Components\Section::make('Kategori ve Marka Bilgileri')
                        ->schema([
                            Forms\Components\Select::make('category_id')
                                ->label('Kategori')
                                ->relationship('category', 'name', function ($query) {
                                    return $query->where('is_active', true);
                                })
                                ->required()
                                ->preload()
                                ->searchable()
                                ->placeholder('Bir kategori seçin'),

                            Forms\Components\Select::make('brand_id')
                                ->label('Marka')
                                ->relationship('brand', 'name', function ($query) {
                                    return $query->where('is_active', true);
                                })
                                ->preload()
                                ->required()
                                ->searchable()
                                ->placeholder('Bir marka seçin'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    // Diğer Özellikler
                    \Filament\Schemas\Components\Section::make()
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),

                            Forms\Components\Toggle::make('is_featured')
                                ->label('Öne Çıkan'),

                            Forms\Components\Toggle::make('is_new')
                                ->label('Yeni Ürün'),

                            Forms\Components\Toggle::make('is_digital')
                                ->label('Dijital Ürün'),

                            Forms\Components\Toggle::make('is_free_shipping')
                                ->label('Ücretsiz Kargo'),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),

                    // SEO Bilgileri
                    \Filament\Schemas\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Başlık')
                                ->maxLength(255),

                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Açıklama')
                                ->rows(3)
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('meta_keywords')
                                ->label('Meta Anahtar Kelimeler')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('search_keywords')
                                ->label('Arama Anahtar Kelimeleri')
                                ->maxLength(255),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    // Ürün Görselleri
                    \Filament\Schemas\Components\Section::make('Ürün Görselleri')
                        ->description('Ürün görsellerini yükleme ve yönetme')
                        ->icon('heroicon-o-photo')
                        ->columnSpanFull()
                        ->schema([
                            Repeater::make('images')
                                ->relationship('images')
                                ->schema([
                                    Forms\Components\FileUpload::make('image_path')
                                        ->label('Ürün Görselleri')
                                        ->image()
                                        ->directory('product/images')
                                        ->maxSize(2048) // 2 MB boyut sınırı
                                        ->reorderable()
                                        ->helperText('Bir veya daha fazla görsel yükleyin.'),

                                ])
                                ->label('Ürün Resimleri'),
                        ]),

                    // Ek Bilgiler
                    \Filament\Schemas\Components\Section::make('Ek Bilgi')
                        ->description('Diğer ürün detayları')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('sku')
                                ->label('SKU yada barkod')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('tags')
                                ->label('Etiketler')
                                ->relationship('tags', 'name')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Etiket Adı')
                                        ->required()
                                        ->unique()
                                        ->maxLength(255),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    return Tag::create($data)->id;
                                })
                                ->multiple()
                                ->preload()
                                ->helperText('Virgül ile ayırarak birden fazla etiket ekleyebilirsiniz.'),

                            Forms\Components\KeyValue::make('specifications')
                                ->label('Özellikler')
                                ->keyLabel('Özellik Adı')
                                ->valueLabel('Özellik Değeri')
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('view_count')
                                ->label('Görüntülenme Sayısı')
                                ->numeric()
                                ->default(0)
                                ->disabled(),
                        ])
                        ->columns(2),

                ]),
            ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('images.image_path')
                    ->label('Resim')
                    ->circular()
                    ->limit(3)
                    ->stacked()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('İsim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->label('Stok')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->label('Marka')
                    ->sortable(),
                Tables\Columns\TextColumn::make('old_price')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                ->label('Aktif Mi?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Öne Çıkan Mı?'),
                Tables\Columns\IconColumn::make('is_new')
                    ->boolean()
                    ->label('Yeni Mi?'),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->label('İndirim')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->label('Değerlendirme'),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('meta_keywords')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('search_keywords')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_digital')
                    ->boolean()
                    ->label('Dijital Mi?'),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label('Görüntülenme Sayısı'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU,Barkod')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_free_shipping')
                    ->boolean()
                    ->label('Kargo Ücretsiz Mi?'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                
                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
                    
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'AKTIF',
                        '0' => 'AKTIF DEGIL',
                    ])
                    ->label('Aktiflik'),
                    
                Filter::make('price')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->label('FIYAT EN AZ'),
                        Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->label('FIYAT EN FAZLA'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
                    
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
