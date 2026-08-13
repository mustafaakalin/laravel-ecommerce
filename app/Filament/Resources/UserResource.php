<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ExportBulkAction;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\Widgets\UserWidget;
use App\Filament\Resources\UserResource\Widgets\UserAnalyticsWidget;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = 'Kullanıcı & Rol & İzin Yönetimi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'Kullanıcı';

    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    protected static ?string $navigationLabel = 'Kullanıcı Yönetimi';


    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Tabs::make('Kullanıcı Yönetimi')
                    ->tabs([
                        Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('İlk adı girin')
                                                    ->label('İlk İsim'),

                                                Forms\Components\TextInput::make('surname')
                                                    ->maxLength(255)
                                                    ->placeholder('Soyadı girin')
                                                    ->label('Soyadı'),
                                            ]),

                                        Forms\Components\TextInput::make('identity_number')
                                            ->maxLength(255)
                                            ->label('Kimlik Numarası')
                                            ->placeholder('Kimlik numarasını girin')
                                            ->unique(ignoreRecord: true)
                                            ->mask('99999999999')
                                            ->hint('11 hane gerekli'),

                                    ]),
                            ]),



                        Tabs\Tab::make('Roller ve İzinler')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Rol Yönetimi')
                                    ->schema([
                                        Select::make('roles')
                                            ->multiple()
                                            ->relationship(
                                                'roles',
                                                'name',
                                                fn($query) => $query->orderBy('name')
                                            )
                                            ->preload()
                                            ->searchable()
                                            ->label('Roller')
                                            ->helperText('Bir veya daha fazla rol seçin')
                                            ->columnSpanFull()
                                            ->loadingMessage('Roller yükleniyor...')
                                            ->searchingMessage('Roller aranıyor...')
                                            ->searchDebounce(500)
                                    ]),

                                Section::make('Doğrudan İzinler')
                                    ->schema([
                                        Select::make('permissions')
                                            ->multiple()
                                            ->relationship(
                                                'permissions',
                                                'name',
                                                fn($query) => $query->orderBy('name')
                                            )
                                            ->preload()
                                            ->searchable()
                                            ->label('Ek İzinler')
                                            ->helperText('Bu kullanıcı için doğrudan izinler')
                                            ->columnSpanFull()
                                            ->loadingMessage('İzinler yükleniyor...')
                                            ->searchingMessage('İzinler aranıyor...')
                                            ->searchDebounce(500)
                                    ])
                            ])
                            ->visible(fn () => auth()->user()->hasRole('admin')),

                        Tabs\Tab::make('Hesap Ayrıntıları')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('E-posta adresinizi girin'),

                                        Forms\Components\DateTimePicker::make('email_verified_at')
                                            ->label('E-posta Onaylı')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $context): bool => $context === 'create')
                                            ->maxLength(255)
                                            ->minLength(8)
                                            ->same('passwordConfirmation')
                                            ->placeholder('Şifre girin'),

                                        Forms\Components\TextInput::make('passwordConfirmation')
                                            ->password()
                                            ->label('Şifre Onayı')
                                            ->required(fn(string $context): bool => $context === 'create')
                                            ->minLength(8)
                                            ->dehydrated(false)
                                            ->placeholder('Şifreyi onayla'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Profil')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->image()
                                            ->imageEditor()
                                            ->circleCropper()
                                            ->directory('avatars')
                                            ->maxSize(5120)
                                            ->label('Profil Fotoğrafı'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Sosyal Medya')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('instagram_account')
                                            ->prefix('@')
                                            ->maxLength(255)
                                            ->placeholder('KULLANICI ADI')
                                            ->suffixIcon('heroicon-m-camera'),

                                        Forms\Components\TextInput::make('facebook_account')
                                            ->prefix('facebook.com/')
                                            ->maxLength(255)
                                            ->placeholder('KULLANICI ADI')
                                            ->suffixIcon('heroicon-m-globe-alt'),

                                        Forms\Components\TextInput::make('tiktok_account')
                                            ->prefix('@')
                                            ->maxLength(255)
                                            ->placeholder('KULLANICI ADI')
                                            ->suffixIcon('heroicon-m-musical-note'),

                                        Forms\Components\TextInput::make('x_account')
                                            ->prefix('@')
                                            ->maxLength(255)
                                            ->placeholder('KULLANICI ADI')
                                            ->suffixIcon('heroicon-m-x-mark'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('surname')
                    ->label('Soyad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Profil Foto')
                    ->circular()
                    ->defaultImageUrl(url('/default-avatar.png'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->toggleable()
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->label('Roller'),

                Tables\Columns\TextColumn::make('identity_number')
                    ->label('TC Kimlik No')
                    ->searchable()
                    ->toggleable()
                    ->visible(fn(): bool => auth()->user()->hasRole('Admin')), // Sadece adminler görebilir

                // Sosyal Medya Hesapları Grubu
                // Sosyal Medya İkonları
                Tables\Columns\IconColumn::make('instagram_account')
                    ->boolean()
                    ->trueIcon('fab-instagram')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('purple')
                    ->label('Instagram')
                    ->url(fn($record) => $record->instagram_account ? "https://instagram.com/{$record->instagram_account}" : null, true),

                Tables\Columns\IconColumn::make('facebook_account')
                    ->boolean()
                    ->trueIcon('fab-facebook')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('blue')
                    ->label('Facebook')
                    ->url(fn($record) => $record->facebook_account ? "https://facebook.com/{$record->facebook_account}" : null, true),

                Tables\Columns\IconColumn::make('tiktok_account')
                    ->boolean()
                    ->trueIcon('fab-tiktok')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('gray')
                    ->label('TikTok')
                    ->url(fn($record) => $record->tiktok_account ? "https://tiktok.com/@{$record->tiktok_account}" : null, true),

                Tables\Columns\IconColumn::make('x_account')
                    ->boolean()
                    ->trueIcon('fab-x-twitter')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('black')
                    ->label('X')
                    ->url(fn($record) => $record->x_account ? "https://x.com/{$record->x_account}" : null, true),


                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Sipariş Sayısı')
                    ->sortable()
                    ->visible(fn () => auth()->user()->hasRole('admin'))
                    ->toggleable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('E-posta Doğrulandı')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('admin')),

                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label('E-posta Durumu')
                    ->nullable()
                    ->queries(
                        true: fn($query) => $query->whereNotNull('email_verified_at'),
                        false: fn($query) => $query->whereNull('email_verified_at'),
                    )
                    ->visible(fn () => auth()->user()->hasRole('admin')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
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
                    ->visible(fn () => auth()->user()->hasRole('admin'))
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->iconButton(),
                    \Filament\Actions\EditAction::make()
                        ->iconButton(),
                    \Filament\Actions\Action::make('password')
                        ->iconButton()
                        ->icon('heroicon-m-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            TextInput::make('new_password')
                                ->password()
                                ->label('Yeni Şifre')
                                ->required()
                                ->minLength(8),
                            TextInput::make('new_password_confirmation')
                                ->password()
                                ->label('Yeni Şifre (Tekrar)')
                                ->required()
                                ->same('new_password')
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'password' => Hash::make($data['new_password'])
                            ]);
                            Notification::make()
                                ->title('Şifre güncellendi')
                                ->success()
                                ->send();
                        }),
                ])
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()->hasRole('admin')),
                    ExportBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make(),
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
            // Admin için tüm kullanıcıların sayısı
            return static::getModel()::count();
        }

        // Normal kullanıcı için sadece 1 (kendi kaydı)
        return '1';
    }


    // Ayrıca kaydetme işlemi için bu methodu ekleyin
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->syncRoles($data['roles'] ?? []);
        $record->syncPermissions($data['permissions'] ?? []);

        unset($data['roles'], $data['permissions']);

        $record->update($data);

        return $record;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $user = static::getModel()::create($data);

        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['permissions'] ?? []);

        return $user;
    }


    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'surname', 'identity_number'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'E-posta' => $record->email,
            'Roller' => $record->roles->pluck('name')->join(', '),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admin değilse sadece kendi kaydını göster
        if (!auth()->user()->hasRole('admin')) {
            $query->where('id', auth()->id());
        }

        return $query;
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


    // Widget'lar ekleyelim
    public static function getWidgets(): array
    {
        return [
            UserWidget::class,
        ];
    }


    protected function getHeaderWidgets(): array
{
    return [
        UserAnalyticsWidget::class,
    ];
}
}
