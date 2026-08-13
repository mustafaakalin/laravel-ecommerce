<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\SoldoutResource\Pages;
use App\Filament\Resources\SoldoutResource\RelationManagers;
use App\Models\Soldout;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class SoldoutResource extends Resource
{
    protected static ?string $model = Soldout::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string | UnitEnum | null $navigationGroup = 'Alışveriş';

    protected static ?string $navigationLabel = 'Satın aldıklarım';
    protected static ?string $modelLabel = 'Satın aldıklarım';
    protected static ?string $pluralModelLabel = 'Satın aldıklarım';

    
    protected static ?string $recordTitleAttribute = 'user_id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Satılan Ürün Detayları')
                    ->description('Satılan ürün bilgilerini yönetin')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Select::make('product_id')
                            ->label('Ürün')
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name'
                            )
                            ->required()
                            ->disabled()
                            ->searchable()
                            ->preload()
                            // ->createOptionForm([
                            //     Forms\Components\TextInput::make('name')
                            //         ->required()
                            //         ->maxLength(255),
                            //     // Ürün oluşturma formunda gerekli diğer alanları ekleyebilirsiniz
                            // ])
                            ->native(false)
                            ->prefixIcon('heroicon-o-cube'),

                        Select::make('user_id')
                            ->label('Kullanıcı')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name'
                            )
                            ->required()
                            ->disabled()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\Toggle::make('is_sold')
                            ->label('Satın Aldım')
                            ->required()
                            ->disabled()
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->inline(false)
                            ->helperText('Ürünün Satın alma durumunu belirtin'),
                    ])
                    ->columns(3),

                Section::make('Notlar')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('notes')
                            ->label('Açıklama')
                            ->placeholder('Ürünün Satın alma ile ilgili notlar...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => "Stok Kodu: " . $record->product?->sku ?? '-')
                    ->icon('heroicon-o-cube'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->user?->email)
                    ->icon('heroicon-o-user'),

                Tables\Columns\IconColumn::make('is_sold')
                    ->label('Durum')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notlar')
                    ->limit(30)
                    ->tooltip(function ($record): ?string {
                        if (strlen($record->notes) > 30) {
                            return $record->notes;
                        }
                        return null;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-calendar'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_sold')
                    ->label('Satın alma Durumu')
                    ->options([
                        '1' => 'Satın aldım',
                        '0' => 'Almadım',
                    ]),

                SelectFilter::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->preload(),

                Filter::make('created_at')
                    ->label('Oluşturma Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Bitiş'),
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
                    }),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->label('Görüntüle')
                        ->icon('heroicon-o-eye'),

                    \Filament\Actions\EditAction::make()
                        ->label('Düzenle')
                        ->icon('heroicon-o-pencil'),

                    \Filament\Actions\Action::make('toggle_status')
                        ->label(fn($record) => $record->is_sold ? 'Stoğa Al' : 'Tükendi Olarak İşaretle')
                        ->icon(fn($record) => $record->is_sold ? 'heroicon-o-arrow-path' : 'heroicon-o-x-circle')
                        ->color(fn($record) => $record->is_sold ? 'success' : 'danger')
                        ->requiresConfirmation()

                        ->visible(fn() => auth()->user()->hasRole('admin'))
                        ->action(fn($record) => $record->update(['is_sold' => !$record->is_sold])),

                    \Filament\Actions\DeleteAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('mark_as_sold')
                        ->label('Tükendi Olarak İşaretle')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_sold' => true]))
                        ->visible(fn() => auth()->user()->hasRole('admin'))
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('mark_as_in_stock')
                        ->label('Stoğa Al')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_sold' => false]))
                        ->visible(fn() => auth()->user()->hasRole('admin'))
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Toplu Sil')
                        ->visible(fn() => auth()->user()->hasRole('admin'))
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->emptyStateHeading('Henüz Satılan Ürün Kaydı Yok')
            ->emptyStateDescription('Satılan ürünler burada listelenecektir.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Yeni Kayıt'),
            ]);
    }







    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('admin')) {
            // Admin için tüm kargoların sayısı
            return static::getModel()::count();
        }

        // Normal kullanıcı için sadece kendi kargolarının sayısı
        return static::getModel()::whereHas('user', function ($query) {
            $query->where('user_id', auth()->id());
        })->count();
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'info';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admin değilse sadece kendi siparişlerine ait kargoları göster
        if (!auth()->user()->hasRole('admin')) {
            $query->whereHas('user', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        return $query;
    }


    // Performans için eloquent sorgusu özelleştirmesi
    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['user', 'product']) // Eager loading ilişkiler
            ->latest();
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoldouts::route('/'),
            'create' => Pages\CreateSoldout::route('/create'),
            'view' => Pages\ViewSoldout::route('/{record}'),
            'edit' => Pages\EditSoldout::route('/{record}/edit'),
        ];
    }
}
