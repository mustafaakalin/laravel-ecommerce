<?php

namespace App\Filament\Resources;

use BackedEnum;
use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Address;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AddressResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AddressResource\RelationManagers;
use App\Filament\Resources\AddressResource\Widgets\AddressWidget;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;


    protected static string | BackedEnum | null $navigationIcon = 'fas-location-dot';

    protected static string | UnitEnum | null $navigationGroup = 'Müşteri Yönetimi';

    protected static ?string $navigationLabel = 'Adresler';

    protected static ?string $modelLabel = 'Adres';

    protected static ?string $pluralModelLabel = 'Adresler';

    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'address';



    public static function form(Schema $schema): Schema
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->dehydrated(true)
                    ->required(),
                Section::make('Kişisel Bilgiler')
                    ->description('Adres sahibinin kişisel bilgileri')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                'user',
                                'name',
                                modifyQueryUsing: fn(Builder $query) =>
                                auth()->user()->hasRole('admin')
                                    ? $query
                                    : $query->where('id', auth()->id())
                            )
                            ->default(fn() => auth()->user()->hasRole('admin') ? null : auth()->id())
                            ->disabled(fn() => !auth()->user()->hasRole('admin'))
                            ->searchable(fn() => auth()->user()->hasRole('admin'))
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email(),
                            ])
                            ->visible(fn() => auth()->user()->hasRole('admin')) // Admin değilse bu alanı tamamen gizle
                            ->label('Kullanıcı'),
                        Select::make('title')
                            ->label('Adres Başlığı')
                            ->options([
                                'home' => 'Ev Adresi',
                                'work' => 'İş Adresi',
                                'summer_house' => 'Yazlık',
                                'family' => 'Aile Evi',
                                'other' => 'Diğer'
                            ])
                            ->native(false)
                            ->placeholder('Adres türünü seçin')
                            ->helperText('Bu adresi tanımlamak için bir başlık seçin')
                            ->default('home')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (string $state, Set $set) {
                                // Eğer başlık "other" ise custom başlık giriş alanını göster
                                $set('show_custom_title', $state === 'other');
                            }),
                        Forms\Components\TextInput::make('first_name')
                            ->label('Ad')
                            ->required()
                            ->minLength(2)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Soyad')
                            ->required()
                            ->minLength(2)
                            ->maxLength(50),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->mask('999 999 99 99')
                            ->placeholder('5XX XXX XX XX')
                            ->required()
                            ->prefix('+90')
                            ->maxLength(15),
                    ]),

                Section::make('Adres Bilgileri')
                    ->description('Detaylı adres bilgileri')
                    ->icon('heroicon-o-map-pin')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->required()
                            ->rows(3)
                            ->minLength(10)
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('country')
                            ->label('Ülke')
                            ->searchable()
                            ->required()
                            ->default('Turkey')
                            ->live()
                            ->options([
                                'Turkey' => 'Türkiye',
                                'Cyprus' => 'Kıbrıs',
                                // Diğer ülkeler eklenebilir
                            ]),

                        Forms\Components\TextInput::make('city')
                            ->label('Şehir')
                            ->required()
                            ->datalist([
                                'İstanbul',
                                'Ankara',
                                'İzmir',
                                'Bursa',
                                'Antalya'
                                // Diğer şehirler eklenebilir
                            ])
                            ->maxLength(50),

                        Forms\Components\TextInput::make('state')
                            ->label('İlçe')
                            ->required()
                            ->maxLength(50),

                        Forms\Components\TextInput::make('zip_code')
                            ->label('Posta Kodu')
                            ->required()
                            ->numeric()
                            ->minLength(5)
                            ->maxLength(5),
                    ]),

                Section::make('Adres Ayarları')
                    ->description('Adres için özel ayarlar')
                    ->icon('heroicon-o-cog')
                    ->schema([
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan Adres')
                            ->helperText('Bu adresi varsayılan adres olarak ayarla')
                            ->default(true)
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->inline(false),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->label('Ilk Isim')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn($record) =>
                    "{$record->first_name} {$record->last_name}")
                    ->description(fn($record) => $record->phone),

                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable()
                    ->label('Adres')
                    ->wrap()
                    ->description(fn($record) =>
                    "{$record->city}, {$record->state} {$record->zip_code}")
                    ->tooltip(fn($record) => $record->country),

                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->label('Varsayılan')
                    ->sortable()
                    ->toggleable()
                    ->size('md'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->searchable()
                    ->label('Ülke')
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Varsayılan Adres'),
            ])
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->authorize(fn($action, $record) => auth()->user()->hasRole('admin') || $record->user_id === auth()->id()),

                    \Filament\Actions\EditAction::make()
                        ->authorize(fn($action, $record) => auth()->user()->hasRole('admin') || $record->user_id === auth()->id()),
                    \Filament\Actions\Action::make('set_default')
                        ->icon('heroicon-o-star')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            // Önce diğer tüm adreslerin varsayılan özelliğini kaldır
                            Address::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_default' => false]);

                            // Sonra seçilen adresi varsayılan yap
                            $record->update(['is_default' => true]);
                        })
                        ->hidden(fn($record) => $record->is_default)
                        ->authorize(fn($action, $record) => auth()->user()->hasRole('admin') || $record->user_id === auth()->id()),

                    \Filament\Actions\DeleteAction::make()
                        ->authorize(fn($action, $record) => auth()->user()->hasRole('admin') || $record->user_id === auth()->id()),
                ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->authorize(fn() => auth()->user()->hasRole('admin')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Önce user_id'yi set edelim
        if (!auth()->user()->hasRole('admin')) {
            $data['user_id'] = auth()->id();
        }

        // Adres sayısı kontrolü
        if (!auth()->user()->hasRole('admin')) {
            $addressCount = auth()->user()->addresses()->count();
            if ($addressCount >= 4) {
                Notification::make()
                    ->warning()
                    ->title('En fazla 4 adres ekleyebilirsiniz')
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Admin olmayan kullanıcılar için user_id'yi zorla set edelim
        if (!auth()->user()->hasRole('admin')) {
            $data['user_id'] = auth()->id();
        }

        // Eğer bu adres varsayılan olarak ayarlanmışsa, diğer adresleri varsayılan olmaktan çıkar
        if ($data['is_default']) {
            Address::where('user_id', $data['user_id'])
                ->where('id', '!=', $this->record?->id)
                ->update(['is_default' => false]);
        }

        return $data;
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

    public static function canCreate(): bool
    {
        if (! auth()->user()->hasRole('admin')) {
            // Check if user has less than 4 addresses
            return auth()->user()->addresses()->count() < 4;
        }

        return true;
    }

    public static function getNavigationBadge(): ?string
    {
        if (! auth()->user()->hasRole('admin')) {
            return auth()->user()->addresses()->count() . '/4';
        }

        return static::getModel()::count();
    }


    public static function getNavigationBadgeColor(): ?string
    {
        return auth()->user()->addresses()->count() > 3 ? 'danger' : 'info';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'view' => Pages\ViewAddress::route('/{record}'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }


    // Widget'lar ekleyelim
    public static function getWidgets(): array
    {
        return [
            AddressWidget::class,
        ];
    }
}
