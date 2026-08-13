<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\ProductImageResource\Pages;
use App\Filament\Resources\ProductImageResource\RelationManagers;
use App\Models\ProductImage;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductImageResource extends Resource
{
    protected static ?string $model = ProductImage::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | UnitEnum | null $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?string $navigationLabel = 'Ürün Görselleri';
    
    protected static ?string $modelLabel = 'Ürün Görseli';
    
    protected static ?string $pluralModelLabel = 'Ürün Görselleri';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'product_id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Ürün')
                    ->relationship('product', 'name') // Product modelindeki name alanını gösterir
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Ürün'),

                Forms\Components\FileUpload::make('image_path')
                    ->image()
                    ->imageEditor() // Resim düzenleme özelliği ekler
                    ->downloadable() // İndirme butonu ekler
                    ->preserveFilenames() // Orijinal dosya adını korur
                    ->directory('product/images') // Resimlerin yükleneceği klasör
                    ->maxSize(5120) // 5MB maksimum boyut
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('JPEG, PNG veya WebP formatında maksimum 5MB')
                    ->required()
                    ->columnSpanFull() // Tam genişlik kullanır
                    ->label('Ürün Görseli'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Görsel'),
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->label('Ürün Adı'),
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
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListProductImages::route('/'),
            'create' => Pages\CreateProductImage::route('/create'),
            'view' => Pages\ViewProductImage::route('/{record}'),
            'edit' => Pages\EditProductImage::route('/{record}/edit'),
        ];
    }
}
