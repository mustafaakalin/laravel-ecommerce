<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\LikeResource\Pages;
use App\Filament\Resources\LikeResource\RelationManagers;
use App\Models\Like;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ViewColumn;

class LikeResource extends Resource
{
    protected static ?string $model = Like::class;


    protected static string | BackedEnum | null $navigationIcon = 'fas-heart';

    protected static string | UnitEnum | null $navigationGroup = 'İçerik Yönetimi';
    
    protected static ?string $navigationLabel = 'Beğeniler';
    
    protected static ?string $modelLabel = 'Beğeni';
    
    protected static ?string $pluralModelLabel = 'Beğeniler';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'user_id';
    

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Section::make('Beğeni Detayları')
                    ->description('Ürün beğeni bilgilerini buradan yönetebilirsiniz.')
                    ->icon('heroicon-o-heart')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->preload()
                            ->visible(fn() => auth()->user()->hasRole('admin'))
                            ->searchable()
                            ->label('Kullanıcı')
                            ->placeholder('Kullanıcı seçiniz')
                            ->columnSpan(1),

                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->preload()
                            ->disabled()
                            ->searchable()
                            ->label('Ürün')
                            ->placeholder('Ürün seçiniz')
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->color('success'),

                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-shopping-bag')
                    ->color('primary'),

                IconColumn::make('created_at')
                    ->label('Beğeni')
                    ->icon('heroicon-s-heart')
                    ->color('danger'),

                TextColumn::make('created_at')
                    ->label('Beğeni Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Kullanıcıya Göre')
                    ->searchable()
                    ->visible(auth()->user()->hasRole('admin'))
                    ->preload()
                    ->multiple(),

                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->label('Ürüne Göre')
                    ->searchable()
                    ->visible(auth()->user()->hasRole('admin'))
                    ->preload()
                    ->multiple(),

                SelectFilter::make('created_at')
                    ->label('Tarih Aralığı')
                    ->options([
                        'today' => 'Bugün',
                        'week' => 'Bu Hafta',
                        'month' => 'Bu Ay',
                        'year' => 'Bu Yıl',
                    ])
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->label('Görüntüle')
                    ->icon('heroicon-o-eye')
                    ->color('success'),

                \Filament\Actions\EditAction::make()
                    ->label('Düzenle')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                \Filament\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil')
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->emptyStateHeading('Henüz beğeni yok')
            ->emptyStateDescription('Ürünlere yapılan beğeniler burada listelenecektir.')
            ->emptyStateIcon('heroicon-o-heart')
            ->poll('30s'); // Her 30 saniyede bir tabloyu yeniler
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
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


    public static function getNavigationBadge(): ?string
{
    if (!auth()->user()->hasRole('admin')) {
        // Normal kullanıcı için sadece kendi Like sayısı
        return Like::where('user_id', auth()->id())->count();
    }
    
    // Admin için tüm Like sayısı
    return Like::count();
}

public static function getNavigationBadgeColor(): ?string
{
    return static::getModel()::count() > 100 ? 'success' : 'info';
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLikes::route('/'),
            'create' => Pages\CreateLike::route('/create'),
            'view' => Pages\ViewLike::route('/{record}'),
            'edit' => Pages\EditLike::route('/{record}/edit'),
        ];
    }
}
