<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Forms\Components\Radio;
// veya
use Filament\Forms\Components\TextInput;


class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string | BackedEnum | null $navigationIcon = 'fas-comments';

    protected static string | UnitEnum | null $navigationGroup = 'İçerik Yönetimi';

    protected static ?string $navigationLabel = 'Yorumlar';

    protected static ?string $modelLabel = 'Yorum';

    protected static ?string $pluralModelLabel = 'Yorumlar';

    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'content';


    public static function getNavigationBadge(): ?string
    {
        if (!auth()->user()->hasRole('admin')) {
            // Normal kullanıcı için sadece kendi yorumlarını sayısı
            return Comment::where('user_id', auth()->id())->count();
        }

        // Admin için tüm yorumlarını sayısı
        return Comment::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'info';
    }


    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->preload()
                    ->label('Kullanıcı'),

                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()

                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->searchable()
                    ->preload()
                    ->label('Ürün'),

                MarkdownEditor::make('content')
                    ->required()
                    ->label('Yorum')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'link',
                        'bulletList',
                        'orderedList',
                    ])
                    ->columnSpanFull(),

                // 1. Seçenek: Radio Buttons ile
                // Radio::make('rating')
                //     ->required()
                //     ->label('Değerlendirme')
                //     ->options([
                //         1 => '⭐',
                //         2 => '⭐⭐',
                //         3 => '⭐⭐⭐',
                //         4 => '⭐⭐⭐⭐',
                //         5 => '⭐⭐⭐⭐⭐',
                //     ])
                //     ->inline()
                //     ->descriptions([
                //         1 => 'Çok Kötü',
                //         2 => 'Kötü',
                //         3 => 'Orta',
                //         4 => 'İyi',
                //         5 => 'Çok İyi',
                //     ]),

                // 2. Seçenek: Number Input ile

                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->label('Değerlendirme')
                    ->minValue(1)
                    ->maxValue(5)
                    ->step(1)
                    ->suffix('/ 5')
                    ->hint('1-5 arası bir değer girin')

            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Yorum')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap(),

                TextColumn::make('rating')
                    ->badge()
                    ->label('Değerlendirme')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->colors([
                        'warning',
                    ])
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('rating')
                    ->label('Değerlendirme')
                    ->options([
                        1 => '⭐ (1)',
                        2 => '⭐⭐ (2)',
                        3 => '⭐⭐⭐ (3)',
                        4 => '⭐⭐⭐⭐ (4)',
                        5 => '⭐⭐⭐⭐⭐ (5)',
                    ]),

                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Kullanıcı')
                    ->visible(auth()->user()->hasRole('admin'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('product')
                    ->relationship('product', 'name')
                    ->label('Ürün')
                    ->searchable()
                    ->visible(auth()->user()->hasRole('admin'))
                    ->preload(),

                TernaryFilter::make('has_content')
                    ->label('Yorum Durumu')
                    ->placeholder('Hepsi')
                    ->trueLabel('Yorumlu')
                    ->visible(auth()->user()->hasRole('admin'))
                    ->falseLabel('Yorumsuz')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('content')->where('content', '!=', ''),
                        false: fn($query) => $query->whereNull('content')->orWhere('content', ''),
                    ),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make()
                    ->color('success'),
                \Filament\Actions\EditAction::make()
                    ->color('warning'),
                \Filament\Actions\DeleteAction::make()
                    ->color('danger'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Henüz yorum eklenmemiş')
            ->emptyStateDescription('Ürünler için yapılan yorumlar burada listelenecektir.')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text');
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'view' => Pages\ViewComment::route('/{record}'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
