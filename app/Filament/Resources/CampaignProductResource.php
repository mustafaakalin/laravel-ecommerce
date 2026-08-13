<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\CampaignProductResource\Pages;
use App\Filament\Resources\CampaignProductResource\RelationManagers;
use App\Models\CampaignProduct;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampaignProductResource extends Resource
{
    protected static ?string $model = CampaignProduct::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | UnitEnum | null $navigationGroup = 'Pazarlama';
    
    protected static ?string $navigationLabel = 'Kampanya Ürünleri';
    
    protected static ?string $modelLabel = 'Kampanya Ürünleri';
    
    protected static ?string $pluralModelLabel = 'Kampanya Ürünleri';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'product.name';

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('campaign_id')
                    ->relationship('campaign', 'name') // Campaign modelinde 'name' alanını gösterecek
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        // Campaign oluşturma formu alanları
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        // Diğer campaign alanları...
                    ])
                    ->label('Kampanya'),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name') // Product modelinde 'name' alanını gösterecek
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        // Product oluşturma formu alanları
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        // Diğer product alanları...
                    ])
                    ->label('Ürün'),

                // İsteğe bağlı olarak eklenebilecek bir card içinde ilişkili bilgileri gösterme
                \Filament\Schemas\Components\Section::make('Detaylar')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->content(fn($record) => $record ? $record->created_at->format('d/m/Y H:i') : '-'),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->content(fn($record) => $record ? $record->updated_at->format('d/m/Y H:i') : '-'),
                    ])
                    ->collapsible()
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('campaign.name')
                    ->label('Kampanya')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // İsteğe bağlı filtreler eklenebilir
                Tables\Filters\SelectFilter::make('campaign')
                    ->relationship('campaign', 'name')
                    ->label('Kampanya'),

                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->label('Ürün'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCampaignProducts::route('/'),
            'create' => Pages\CreateCampaignProduct::route('/create'),
            'view' => Pages\ViewCampaignProduct::route('/{record}'),
            'edit' => Pages\EditCampaignProduct::route('/{record}/edit'),
        ];
    }
}
