<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use App\Models\ProductAttributeValue;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\QueryBuilder\Constraints;
use App\Filament\Resources\ProductAttributeValueResource\Pages;
use App\Filament\Resources\ProductAttributeValueResource\RelationManagers;


class ProductAttributeValueResource extends Resource
{
    protected static ?string $model = ProductAttributeValue::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string | UnitEnum | null $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?string $navigationLabel = 'Özellik Değerleri';
    
    protected static ?string $modelLabel = 'Özellik Değeri';
    
    protected static ?string $pluralModelLabel = 'Özellik Değerleri';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $recordTitleAttribute = 'value';

    
    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Section::make('Özellik Değeri')
                    ->description('Ürün özellik değerini yapılandırın')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name'
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->label('Ürün')
                            ->columnSpanFull(),

                        Select::make('attribute_id')
                            ->relationship(
                                name: 'attribute',
                                titleAttribute: 'name'
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('value', null))
                            ->label('Özellik'),

                        TextInput::make('value')
                            ->required()
                            ->maxLength(255)
                            ->label('Değer')
                            ->hint('Özellik için bir değer girin')
                            ->hintIcon('heroicon-m-information-circle'),
                    ]),

                Section::make('Kayıt Bilgileri')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->content(fn($record): ?string =>
                            $record?->created_at?->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->content(fn($record): ?string =>
                            $record?->updated_at?->diffForHumans()),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn($record) => $record !== null)
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->grow(false),
    
                TextColumn::make('attribute.name')
                    ->label('Özellik')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->grow(false),
    
                TextColumn::make('value')
                    ->label('Değer')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->copyable()
                    ->copyMessage('Değer kopyalandı')
                    ->color('success'),
    
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->since(),
    
                TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Ürüne Göre'),
    
                SelectFilter::make('attribute')
                    ->relationship('attribute', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Özelliğe Göre'),
    
                QueryBuilder::make()
                    ->constraints([
                        QueryBuilder\Constraints\TextConstraint::make('value')
                            ->label('Değer')
                            ->icon('heroicon-m-document-text'),
                        QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->icon('heroicon-m-calendar'),
                    ])
            ])
            ->filtersFormColumns(3)
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->modalContent(fn (ProductAttributeValue $record): string => "
                            Ürün: {$record->product->name}<br>
                            Özellik: {$record->attribute->name}<br>
                            Değer: {$record->value}
                        ")
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false),
                        
                    \Filament\Actions\EditAction::make()
                        ->modalWidth('lg'),
                        
                    \Filament\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                        
                    \Filament\Actions\BulkAction::make('updateValue')
                        ->label('Toplu Değer Güncelle')
                        ->icon('heroicon-m-pencil')
                        ->form([
                            Forms\Components\TextInput::make('value')
                                ->label('Yeni Değer')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['value' => $data['value']]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->poll('60s')
            ->striped()
            ->persistSortInSession()
            ->selectCurrentPageOnly()
            ->defaultPaginationPageOption(25);
    }

// Resource sınıfına eklenecek diğer metodlar



    // İlişkili modelleri göstermek için getTitleAttribute metodunu ekleyin
    public static function getGloballySearchableAttributes(): array
    {
        return ['value', 'product.name', 'attribute.name'];
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'info';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductAttributeValues::route('/'),
            'create' => Pages\CreateProductAttributeValue::route('/create'),
            'view' => Pages\ViewProductAttributeValue::route('/{record}'),
            'edit' => Pages\EditProductAttributeValue::route('/{record}/edit'),
        ];
    }
}
