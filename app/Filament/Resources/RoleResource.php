<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Spatie\Permission\Models\Role;
use Filament\Tables;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;


    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    
    protected static string | UnitEnum | null $navigationGroup = 'Kullanıcı & Rol & İzin Yönetimi';
    
    // Navigasyon sıralaması (User:1, Role:2, Permission:3)
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'name';
    
    protected static ?string $modelLabel = 'Rol';
    
    protected static ?string $pluralModelLabel = 'Roller';
    
    protected static ?string $navigationLabel = 'Rol Yönetimi';
    
    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                \Filament\Schemas\Components\Section::make('Rol Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Rol Adı')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Rol adı benzersiz olmalıdır')
                            ->live()
                            ->afterStateUpdated(
                                fn(Get $get, Set $set) =>
                                $set('guard_name', $get('guard_name') ?? config('auth.defaults.guard'))
                            ),

                        Forms\Components\Select::make('guard_name')
                            ->label('Guard')
                            ->options([
                                'web' => 'Web Guard',
                                'api' => 'API Guard',
                                'sanctum' => 'Sanctum Guard',
                                // Diğer guardları buraya ekleyebilirsiniz
                            ])
                            ->default(config('auth.defaults.guard'))
                            ->required()
                            ->helperText('Guard türünü seçiniz'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('İzinler')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->label('Rol İzinleri')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->gridDirection('row')
                            ->helperText('Bu role atanacak izinleri seçiniz'),
                    ])
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Rol İstatistikleri')
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->content(
                                fn(?Role $record): string =>
                                $record ? $record->created_at->format('d/m/Y H:i') : '-'
                            ),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->content(
                                fn(?Role $record): string =>
                                $record ? $record->updated_at->format('d/m/Y H:i') : '-'
                            ),

                        Forms\Components\Placeholder::make('user_count')
                            ->label('Kullanıcı Sayısı')
                            ->content(
                                fn(?Role $record): string =>
                                $record ? $record->users()->count() . ' kullanıcı' : '-'
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
                    ->label('Rol Adı')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'web' => 'success',
                        'api' => 'info',
                        'sanctum' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('İzin Sayısı')
                    ->counts('permissions')
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Kullanıcı Sayısı')
                    ->counts('users')
                    ->sortable(),

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
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        // Silmeden önce ilişkili izinleri ve kullanıcıları temizle
                        $record->permissions()->detach();
                        $record->users()->detach();
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            // Toplu silmeden önce ilişkili izinleri ve kullanıcıları temizle
                            $records->each(function (Role $record) {
                                $record->permissions()->detach();
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
