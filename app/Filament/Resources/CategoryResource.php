<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string | BackedEnum | null $navigationIcon = 'fas-layer-group';

    protected static string | UnitEnum | null $navigationGroup = 'Katalog Yönetimi';

    protected static ?string $navigationLabel = 'Kategoriler';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategoriler';

    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'name';

    // Navigation badge - aktif kategori sayısını gösterir

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Kategori Adı')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->label('Üst Kategori')
                    ->preload()
                    ->default(null),
                TextInput::make('slug')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('fa-solid fa-house')
                    ->label('Kategori Simge, icon')
                    ->helperText('https://fontawesome.com/search?m=free')
                    ->default(null),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Kategori Açıklama'),
                TextInput::make('products_count')
                    ->numeric()
                    ->default(null)
                    ->hidden(),
                Toggle::make('is_active')
                    ->required()
                    ->label('Aktiflik'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Kategori Adı')
                    ->description(fn($record) => $record->description ? Str::limit($record->description, 50) : '')
                    ->copyable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->sortable()
                    ->label('Üst Kategori')
                    ->default('Root Category')
                    ->color('gray'),

                // Tables\Columns\IconColumn::make('icon')
                //     ->searchable()
                //     ->tooltip('Icon')
                //     ->alignCenter(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Ürünler')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Durum')
                    ->onColor('success')
                    ->offColor('danger'),

                // Tables\Columns\TextColumn::make('sort_order')
                //     ->sortable()
                //     ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Üst Kategori')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('INaktifler')
                    ->native(false),

                Tables\Filters\Filter::make('has_products')
                    ->query(fn(Builder $query) => $query->has('products'))
                    ->label('Ürünlere Sahip')
                    ->toggle(),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),

                    \Filament\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),

                    \Filament\Actions\Action::make('clone')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->action(function ($record) {
                            $clone = $record->replicate();
                            $clone->name = "{$record->name} (Copy)";
                            $clone->slug = Str::slug($clone->name);
                            $clone->save();
                        }),

                    \Filament\Actions\Action::make('products')
                    ->label('Ürünleri Görüntüle')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn($record) => route('filament.admin.resources.products.index', [
                        'tableFilters[category][value]' => $record->id
                    ]))
                    ->openUrlInNewTab(),

                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('activate')
                        ->label('Seçileni Etkinleştir')
                        ->icon('heroicon-o-check')
                        ->action(fn($records) => $records->each->update(['is_active' => true])),
                    \Filament\Actions\BulkAction::make('deactivate')
                        ->label('Seçileni Devre Dışı Bırak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-folder')
            ->emptyStateHeading('Henüz Kategori Yok')
            ->emptyStateDescription('Başlamak için ilk kategorinizi oluşturun.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Kategori Oluştur')
                    ->icon('heroicon-o-plus'),
            ]);
    }


    // Resource sınıfınıza ekleyin
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
