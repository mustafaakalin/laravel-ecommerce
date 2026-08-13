<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
// use App\Models\Permission;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;


class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-key';
    
    protected static string | UnitEnum | null $navigationGroup = 'Kullanıcı & Rol & İzin Yönetimi';
    
    // Navigasyon sıralaması (User:1, Role:2, Permission:3)
    protected static ?int $navigationSort = 3;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $modelLabel = 'İzin';
    
    protected static ?string $pluralModelLabel = 'İzinler';
    
    protected static ?string $navigationLabel = 'İzin Yönetimi';
    


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['roles']);  // users_count'u kaldırdık
    }

    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('İzin Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('İzin Adı')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('İzin adı benzersiz olmalıdır')
                            ->placeholder('örn: users.create')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => 
                                $set('guard_name', $get('guard_name') ?? config('auth.defaults.guard'))
                            ),
    
                        Forms\Components\Select::make('guard_name')
                            ->label('Guard')
                            ->options([
                                'web' => 'Web Koruması',
                                'api' => 'API Koruması',
                                'sanctum' => 'Sanctum Muhafızı',
                            ])
                            ->default(config('auth.defaults.guard'))
                            ->required()
                            ->helperText('Koruma türünü seçiniz'),
                            
                        // Forms\Components\Textarea::make('description')
                        //     ->label('Açıklama')
                        //     ->maxLength(1000)
                        //     ->columnSpanFull()
                        //     ->helperText('İznin ne işe yaradığını açıklayın'),
                    ])->columns(2),
    
                \Filament\Schemas\Components\Section::make('Rol Atamaları')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Roller')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Bu izne sahip olacak rolleri seçiniz'),
                    ])
                    ->collapsible(),
    
                \Filament\Schemas\Components\Section::make('İzin İstatistikleri')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->content(fn (?Permission $record): string => 
                                $record ? $record->created_at->format('d/m/Y H:i') : '-'
                            ),
    
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->content(fn (?Permission $record): string => 
                                $record ? $record->updated_at->format('d/m/Y H:i') : '-'
                            ),
    
                        Forms\Components\Placeholder::make('usage_info')
                            ->label('Kullanım')
                            ->content(fn (?Permission $record): string => 
                                $record ? sprintf(
                                    '%d rol atanmış',
                                    $record->roles()->count()
                                ) : '-'
                            ),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('İzin Adı')
                    ->searchable()
                    ->sortable()
                    ->description(
                        fn(Permission $record): string =>
                        $record->description ?? ''
                    ),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'web' => 'success',
                        'api' => 'info',
                        'sanctum' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('roles_count')
                    ->label('Rol Sayısı')
                    ->sortable()
                    ->alignCenter(),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web Guard',
                        'api' => 'API Guard',
                        'sanctum' => 'Sanctum Guard',
                    ])
                    ->label('Guard'),

                Tables\Filters\Filter::make('created_at')
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
                    })
                    ->label('Oluşturulma Tarihi'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->before(function (Permission $record) {
                        $record->roles()->detach();
                        $record->users()->detach();
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $records->each(function (Permission $record) {
                                $record->roles()->detach();
                                $record->users()->detach();
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
