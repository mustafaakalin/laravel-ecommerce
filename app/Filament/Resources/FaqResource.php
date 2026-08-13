<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;



    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-question-mark-circle';
    
    protected static string | UnitEnum | null $navigationGroup = 'Sayfalar';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'question';
    
    protected static ?string $modelLabel = 'SSS';
    
    protected static ?string $pluralModelLabel = "Sıkça Sorulan Sorular";
    
    protected static ?string $navigationLabel = 'SSS Yönetimi';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->required()
                    ->label('Soru')
                    ->maxLength(255),
                Forms\Components\Textarea::make('answer')
                    ->required()
                    ->label('Cevap')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                ->label('Soru')
                    ->searchable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }



    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Soru' => $record->question,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'view' => Pages\ViewFaq::route('/{record}'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
