<?php

namespace App\Filament\Resources;

use BackedEnum;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Campaign;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CampaignResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Filament\Resources\CampaignProductResource\Widgets\CampaignProductWidget;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';
    
    protected static ?string $recordTitleAttribute = 'name';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Kampanya Bilgileri')
                    ->description('Kampanya detaylarını bu alandan yönetebilirsiniz')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kampanya Adı')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Örn: Yaz Sezonu İndirimi')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if (!$get('is_slug_changed_manually') && filled($get('name'))) {
                                    $set('slug', Str::slug($get('name')));
                                }
                            })
                            ->columnSpan(['lg' => 2]),

                        Forms\Components\RichEditor::make('description')
                            ->label('Kampanya Açıklaması')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->placeholder('Kampanya detaylarını buraya giriniz...')
                            ->columnSpan(['lg' => 2]),
                    ])
                    ->columns(2),

                Section::make('İndirim Detayları')
                    ->description('İndirim türü ve değerini buradan belirleyebilirsiniz')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Select::make('discount_type')
                            ->label('İndirim Türü')
                            ->options([
                                'percentage' => 'Yüzde İndirimi (%)',
                                'fixed' => 'Sabit İndirim (₺)',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('İndirim Değeri')
                            ->required()
                            ->numeric()
                            ->prefix(fn(Get $get) => $get('discount_type') === 'percentage' ? '%' : '₺')
                            ->minValue(0)
                            ->maxValue(fn(Get $get) => $get('discount_type') === 'percentage' ? 100 : null)
                            ->step(0.01)
                            ->placeholder(
                                fn(Get $get) =>
                                $get('discount_type') === 'percentage' ? '0-100 arası bir değer giriniz' : 'İndirim tutarını giriniz'
                            ),
                    ])
                    ->columns(2),

                Section::make('Kampanya Süresi')
                    ->description('Kampanyanın geçerlilik süresini buradan belirleyebilirsiniz')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Başlangıç Tarihi')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->timezone('Europe/Istanbul')
                            ->displayFormat('d.m.Y H:i')
                            ->default(now())
                            ->beforeStateDehydrated(function ($state) {
                                return $state ? Carbon::parse($state)->setTimezone('UTC') : null;
                            })
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state($state ? Carbon::parse($state)->setTimezone('Europe/Istanbul') : null);
                            })
                            ->rules([
                                'required',
                                'date',
                            ])
                            ->live(),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Bitiş Tarihi')
                            ->native(false)
                            ->seconds(false)
                            ->timezone('Europe/Istanbul')
                            ->displayFormat('d.m.Y H:i')
                            ->beforeStateDehydrated(function ($state) {
                                return $state ? Carbon::parse($state)->setTimezone('UTC') : null;
                            })
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state($state ? Carbon::parse($state)->setTimezone('Europe/Istanbul') : null);
                            })
                            ->rules([
                                'nullable',
                                'date',
                                'after:start_date',
                            ])
                            ->helperText('Boş bırakılırsa süresiz olacaktır'),
                    ])
                    ->columns(2),

                Section::make('Ürün Seçimi')
                    ->description('Kampanyaya dahil edilecek ürünleri seçin')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Forms\Components\Select::make('products')
                            ->label('Kampanya Ürünleri')
                            ->multiple()
                            ->relationship('products', 'name')
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₺'),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Kampanya Durumu')
                    ->description('Kampanyanın aktiflik durumunu buradan yönetebilirsiniz')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kampanya Aktif Mi?')
                            ->required()
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->helperText(new HtmlString('Bu kampanya yayında olacak mı? <br>Aktif olmayan kampanyalar müşteriler tarafından görüntülenemez.')),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kampanya Adı')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => Str::limit($record->description, 50))
                    ->wrap(),

                Tables\Columns\TextColumn::make('discount_type')
                    ->label('İndirim Türü')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'percentage' => 'Yüzde İndirimi',
                        'fixed' => 'Sabit İndirim',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('discount_value')
                    ->label('İndirim Değeri')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: ',',
                        thousandsSeparator: '.'
                    )
                    ->sortable()
                    ->suffix(fn($record) => $record->discount_type === 'percentage' ? '%' : ' ₺'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->description(
                        fn($record) => $record->end_date ?
                            'Bitiş: ' . $record->end_date->format('d.m.Y H:i') :
                            'Süresiz Kampanya'
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->alignCenter()
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('İndirim Türü')
                    ->options([
                        'percentage' => 'Yüzde İndirimi',
                        'fixed' => 'Sabit İndirim',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik Durumu')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif Kampanyalar')
                    ->falseLabel('Pasif Kampanyalar'),

                Tables\Filters\Filter::make('active_campaigns')
                    ->label('Güncel Kampanyalar')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('is_active', true)
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>', now());
                            })
                            ->where('start_date', '<=', now())
                    ),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Başlangıç Tarihi')
                            ->displayFormat('d.m.Y'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Bitiş Tarihi')
                            ->displayFormat('d.m.Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->label('Görüntüle'),

                    \Filament\Actions\EditAction::make()
                        ->label('Düzenle'),

                    \Filament\Actions\Action::make('toggle_status')
                        ->label(fn($record) => $record->is_active ? 'Pasife Al' : 'Aktife Al')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        }),

                    \Filament\Actions\DeleteAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('activate')
                        ->label('Seçilenleri Aktif Et')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('deactivate')
                        ->label('Seçilenleri Pasif Et')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-megaphone')
            ->emptyStateHeading('Henüz Kampanya Oluşturulmadı')
            ->emptyStateDescription('Kampanyalarınızı buradan yönetebilirsiniz.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Yeni Kampanya'),
            ]);
    }


    // Resource sınıfınıza ekleyin
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Performans için
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->withCount('products');
    }



    // Resource sınıfına eklenecek özellikler
    public static function getNavigationLabel(): string
    {
        return 'Kampanyalar';
    }

    public static function getModelLabel(): string
    {
        return 'Kampanya';
    }


    public static function getNavigationGroup(): ?string
    {
        return 'Pazarlama';
    }

    protected static ?int $navigationSort = 3;



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'view' => Pages\ViewCampaign::route('/{record}'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    
    // Widget'lar ekleyelim
    public static function getWidgets(): array
    {
        return [
            CampaignProductWidget::class,
        ];
    }

}
