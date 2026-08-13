<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Models\ProductAttribute;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Filament\Resources\ProductAttributeResource\RelationManagers;

class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string | UnitEnum | null $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?string $navigationLabel = 'Özellikler';
    
    protected static ?string $modelLabel = 'Özellik';
    
    protected static ?string $pluralModelLabel = 'Özellikler';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Öznitelik Detayları')
                    ->description('Ürün özelliklerinin tanımlanması')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Öznitelik Adı')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('örneğin, Renk, Boyut, Malzeme')
                            ->autocapitalize()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Değer Türü')
                            ->options([
                                'select' => 'Seçiniz (Tek Seçenek)',
                                'multiselect' => 'Çoklu Seçim',
                                'text' => 'Metin Girişi',
                                'number' => 'Sayı',
                                'boolean' => 'Evet/Hayır',
                            ])
                            ->required()
                            ->default('select')
                            ->helperText('Bu özelliğin nasıl görüntüleneceğini seçin')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_required')
                            ->label('Gerekli Nitelik')
                            ->default(false)
                            ->helperText('Bu özelliği ürünler için zorunlu hale getirin'),

                        Forms\Components\Toggle::make('is_filterable')
                            ->label('Filtrelenebilir')
                            ->default(true)
                            ->helperText('Ürünleri bu özelliğe göre filtrelemeye izin ver'),
                    ]),

                \Filament\Schemas\Components\Section::make('Öznitelik Değerleri')
                    ->description('Bu öznitelik için önceden tanımlanmış değerleri yönetme')
                    ->schema([
                        Forms\Components\Repeater::make('values')
                            ->relationship('values')
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Öznitelik değerini girin'),

                                Forms\Components\ColorPicker::make('color_code')
                                    ->visible(fn(Get $get) => $get('../../type') === 'select')
                                    ->helperText('Yalnızca renk nitelikleri için'),

                                Forms\Components\Toggle::make('is_default')
                                    ->label('Varsayılan Değer'),
                            ])
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['value'] ?? null)
                            ->visible(fn(Get $get) => in_array($get('type'), ['select', 'multiselect']))
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Özellik Adı')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('values_count')
                    ->label('Değer Sayısı')
                    ->counts('values')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('values.name')
                    ->label('Değerler')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since(),
            ])
            ->filters([
                // Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('value_count')
                    ->label('Değer Durumu')
                    ->options([
                        'with_values' => 'Değeri Olanlar',
                        'without_values' => 'Değeri Olmayanlar',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'with_values') {
                            $query->has('values');
                        }
                        if ($data['value'] === 'without_values') {
                            $query->doesntHave('values');
                        }
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->iconButton(),
                \Filament\Actions\EditAction::make()
                    ->iconButton(),
                \Filament\Actions\Action::make('values')
                    ->label('Değerler')
                    ->icon('heroicon-m-list-bullet')
                    ->color('success')
                    ->iconButton()
                    ->url(fn(ProductAttribute $record): string =>
                    ProductAttributeValueResource::getUrl('index', ['attribute_id' => $record->id]))
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    \Filament\Actions\RestoreBulkAction::make(),
                    \Filament\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            ->striped();
    }



    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 1 ? 'success' : 'info';
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
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'view' => Pages\ViewProductAttribute::route('/{record}'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
