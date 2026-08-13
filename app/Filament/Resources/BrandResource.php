<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Filament\Resources\BrandResource\Widgets\BrandWidget;
use App\Models\Brand;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string | UnitEnum | null $navigationGroup = 'Katalog Yönetimi';
    
    protected static ?string $navigationLabel = 'Markalar';
    
    protected static ?string $modelLabel = 'Marka';
    
    protected static ?string $pluralModelLabel = 'Markalar';
    
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Section::make('Marka Detayları')
                        ->description('Marka hakkında temel bilgiler')
                        ->icon('heroicon-o-shopping-bag')
                        ->columnSpan(1)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                    if ($operation === 'create') {
                                        $set('slug', Str::slug($state));
                                    }
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->dehydrated()
                                ->helperText('Slug, addan otomatik olarak oluşturulacaktır.')
                                ->lazy(),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Marka Durumu')
                                ->helperText('Aktif markalar web sitesinde gösterilecektir')
                                ->default(true)
                                ->onColor('success')
                                ->offColor('danger'),
                        ]),

                    Section::make('Medya ve Açıklama')
                        ->description('Marka logosu ve açıklaması')
                        ->icon('heroicon-o-photo')
                        ->columnSpan(1)
                        ->schema([
                            FileUpload::make('logo')
                                ->image()
                                ->imageEditor()
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('400')
                                ->imageResizeTargetHeight('400')
                                ->directory('brands/logos')
                                ->preserveFilenames()
                                ->maxSize(1024)
                                ->helperText('Önerilen boyut: 400x400px. Maksimum boyut: 1MB')
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('description')
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'link',
                                    'unlink',
                                    'orderedList',
                                    'unorderedList',
                                    'redo',
                                    'undo'
                                ])
                                ->label('Açıklama')
                                ->columnSpanFull(),
                        ]),
                ]),

                Section::make('İstatistikler')
                    ->description('Marka ile ilgili istatistikler')
                    ->icon('heroicon-o-chart-bar')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Placeholder::make('products_count')
                            ->label('Toplam Ürünler')
                            ->content(fn($record) => $record?->products()->count() ?? 0),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last Modified')
                            ->content(fn($record) => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    // İsteğe bağlı olarak mutator ekleyebilirsiniz
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name))
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Marka Adı')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->description(fn($record) => Str::limit($record->description, 40))
                    ->wrap(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Ürünler')
                    ->sortable()
                    ->alignCenter()
                    ->color('success')
                    ->description(fn($record) => 'Son eklenen: ' . optional($record->products()->latest()->first())?->created_at?->diffForHumans() ?? 'N/A'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Durum')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturuldu')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false),

                Tables\Filters\Filter::make('has_products')
                    ->label('Ürünlerle')
                    ->query(fn(Builder $query) => $query->has('products'))
                    ->toggle(),

                Tables\Filters\Filter::make('created_from')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Oluşturulduğu Yer'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        );
                    }),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),

                    \Filament\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),

                    \Filament\Actions\Action::make('products')
                        ->label('Ürünleri Görüntüle')
                        ->icon('heroicon-o-shopping-bag')
                        ->url(fn($record) => route('filament.admin.resources.products.index', [
                            'tableFilters[brand][value]' => $record->id
                        ]))
                        ->openUrlInNewTab(),

                    \Filament\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    \Filament\Actions\BulkAction::make('activate')
                        ->label('Seçileni Etkinleştir')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => true])),

                    \Filament\Actions\BulkAction::make('deactivate')
                        ->label('Seçileni Devre Dışı Bırak')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => false])),

                    \Filament\Actions\BulkAction::make('export')
                        ->label('Seçilenleri Dışa Aktar')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic here
                            return response()->streamDownload(function () use ($records) {
                                echo $records->toJson(JSON_PRETTY_PRINT);
                            }, 'brands.json');
                        }),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('Henüz Marka Yok')
            ->emptyStateDescription('Başlamak için ilk markanızı oluşturun.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Marka Oluşturun')
                    ->icon('heroicon-o-plus'),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'view' => Pages\ViewBrand::route('/{record}'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }

        // Widget'lar ekleyelim
        public static function getWidgets(): array
        {
            return [
                BrandWidget::class,
            ];
        }
}
