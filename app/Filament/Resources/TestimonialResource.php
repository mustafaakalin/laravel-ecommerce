<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Resources\TestimonialResource\RelationManagers;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string | BackedEnum | null $navigationIcon = 'fas-star';

    protected static string | UnitEnum | null $navigationGroup = 'İçerik Yönetimi';
    
    protected static ?string $navigationLabel = 'Müşteri Yorumları';
    
    protected static ?string $modelLabel = 'Müşteri Yorumu';
    
    protected static ?string $pluralModelLabel = 'Müşteri Yorumları';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'author';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('is_active', true)->exists() 
            ? 'success'
            : 'danger';
    }

    
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('author')
                    ->label('Yazar')
                    ->placeholder('Yazar adını girin')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                Forms\Components\TextInput::make('position')
                    ->label('Pozisyon')
                    ->placeholder('Pozisyonu veya unvanı girin')
                    ->required()
                    ->maxLength(255),

                Forms\Components\RichEditor::make('content')
                    ->label('İçerik')
                    ->placeholder('Buraya referans içeriği yazın...')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('avatar')
                    ->label('Avatar')
                    ->image()
                    ->disk('public')
                    ->directory('testimonial/avatars')
                    ->required(),

                // Derecelendirme için Select bileşeni
                Forms\Components\Select::make('rating')
                    ->label('Değerlendirme')
                    ->required()
                    ->options([
                        1 => '⭐',
                        2 => '⭐⭐',
                        3 => '⭐⭐⭐',
                        4 => '⭐⭐⭐⭐',
                        5 => '⭐⭐⭐⭐⭐',
                    ])
                    ->default(5), // Varsayılan olarak 5 yıldız

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif Durum')
                    ->default(true)
                    ->inline(false)
                    ->helperText('Bu referansı web sitesinde görüntülemek için etkin olarak ayarlayın'),
            ])
            ->columns([
                'sm' => 2,
                'lg' => 3,
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author')
                    ->label('Yazar')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Pozisyon')
                    ->searchable()
                    ->sortable(),

                // Avatar alanı için resim önizlemesi
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->size(50) // Resim boyutu ayarı
                    ->circular(), // Yuvarlak görünüme geçiş

                // Rating için yıldız sembolleri
                Tables\Columns\TextColumn::make('rating')
                    ->label('Değerlendirme')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state)) // Yıldız sembolleriyle gösterim
                    ->sortable(),

                // is_active alanı için renkli ikonlar
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturuldu')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenmiş')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Aktif ve Pasif filtreleri ekleyerek kolay veri filtreleme
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->label('Status'),
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



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'view' => Pages\ViewTestimonial::route('/{record}'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
