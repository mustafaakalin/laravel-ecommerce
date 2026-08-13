<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\ShipmentDiscountResource\Pages;
use App\Filament\Resources\ShipmentDiscountResource\RelationManagers;
use App\Models\ShipmentDiscount;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentDiscountResource extends Resource
{
    protected static ?string $model = ShipmentDiscount::class;
    
    protected static ?string $navigationLabel = 'Kargo Ücretsiz mi ?';
    
    protected static ?string $modelLabel = 'Kargo Ücretsiz mi ?';
    
    
    protected static ?string $recordTitleAttribute = 'price';
    protected static ?string $pluralModelLabel = 'Kargo Ücretsiz mi ?';
    


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₺'),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('price')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    // \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListShipmentDiscounts::route('/'),
            // 'create' => Pages\CreateShipmentDiscount::route('/create'),
            'view' => Pages\ViewShipmentDiscount::route('/{record}'),
            'edit' => Pages\EditShipmentDiscount::route('/{record}/edit'),
        ];
    }
}
