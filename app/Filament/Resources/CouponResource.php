<?php

namespace App\Filament\Resources;

use BackedEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Coupon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;
use Illuminate\Validation\Rules\Unique;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CouponResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CouponResource\RelationManagers;


class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-gift-top';
    
    protected static ?string $recordTitleAttribute = 'value';

    protected static ?string $modelLabel = 'İndirim Kuponu';
    
    protected static ?string $pluralModelLabel = 'Kupon ';
    
    protected static ?string $navigationLabel = 'Kupon';
    
public static function form(Schema $schema): Schema
{
    return $form
        ->schema([
            \Filament\Schemas\Components\Section::make('Kupon Detayları')
                ->description('Kupon bilgileri oluşturma veya düzenleme')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->unique(ignorable: fn ($record) => $record)
                        ->maxLength(255)
                        ->placeholder('Kupon kodunu girin')
                        ->autocapitalize()
                        ->label('Kupon Kodu')
                        ->prefixIcon('heroicon-o-ticket'),

                    Forms\Components\Select::make('type')
                        ->required()
                        ->options([
                            'percentage' => 'Yüzde İndirim',
                            'fixed' => 'Sabit Tutar İndirimi',
                        ])
                        ->label('İndirim Tipi')
                        ->prefixIcon('heroicon-o-tag'),

                    Forms\Components\TextInput::make('value')
                        ->required()
                        ->numeric()
                        ->label('İndirim Değeri')
                        ->integer() // Tam sayı olmasını sağlarız.
                        ->minValue(0)
                        ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : '₺')
                        ->prefixIcon('heroicon-o-currency-bangladeshi')
                        ->rules([
                            fn ($get) => function ($attribute, $value, $fail) use ($get) {
                                if ($get('type') === 'percentage' && $value > 100) {
                                    $fail("Yüzde 100'den büyük olamaz");
                                }
                            },
                        ]),
                ])->columns(3),

            \Filament\Schemas\Components\Section::make('Kullanım Sınırları')
                ->schema([
                    Forms\Components\TextInput::make('usage_limit')
                        ->label('Maksimum Kullanım Limiti')
                        ->helperText('Sınırsız kullanım için boş bırakın')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Sınırsız')
                        ->prefixIcon('heroicon-o-users'),

                    Forms\Components\TextInput::make('used_count')
                        ->numeric()
                        ->default(0)
                        ->disabled()
                        ->dehydrated(false)
                        ->prefixIcon('heroicon-o-check-circle'),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Geçerlilik Süresi')
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Geçerlilik Tarihi')
                        ->prefixIcon('heroicon-o-calendar')
                        ->default(now())
                        ->required(),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Şu Tarihe Kadar Geçerlidir')
                        ->prefixIcon('heroicon-o-calendar')
                        ->afterOrEqual('starts_at')
                        ->required()
                        ->helperText('Başlangıç tarihinden sonra olmalı'),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif Durum')
                        ->helperText('Bu kuponu etkinleştirmek veya devre dışı bırakmak için geçiş yapın')
                        ->default(true)
                        ->required(),
                ]),
        ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('code')
                ->label('Kupon Kodu')
                ->searchable()
                ->copyable()
                ->icon('heroicon-o-ticket')
                ->weight('bold')
                ->copyMessage('Kupon kodu kopyalandı')
                ->copyMessageDuration(1500),

            Tables\Columns\TextColumn::make('type')
                ->label('Tip')
                ->badge()
                ->formatStateUsing(fn (string $state): string => 
                    match ($state) {
                        'percentage' => 'Yüzde İndirim',
                        'fixed' => 'Sabit İndirim',
                        default => $state,
                    }
                )
                ->color(fn (string $state): string => 
                    match ($state) {
                        'percentage' => 'warning',
                        'fixed' => 'success',
                        default => 'primary',
                    }
                ),

            Tables\Columns\TextColumn::make('value')
                ->label('Değer')
                ->numeric()
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => 
                    $record->type === 'percentage' 
                        ? "%{$state}" 
                        : "₺{$state}"
                ),

            Tables\Columns\TextColumn::make('used_count')
                ->label('Kullanım')
                ->numeric()
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => 
                    $record->usage_limit 
                        ? "{$state} / {$record->usage_limit}"
                        : $state
                )
                ->color(fn ($record) => 
                    $record->usage_limit && $record->used_count >= $record->usage_limit
                        ? 'danger'
                        : 'success'
                ),

            Tables\Columns\TextColumn::make('starts_at')
                ->label('Başlangıç')
                ->dateTime('d M Y H:i')
                ->sortable()
                ->icon('heroicon-o-calendar'),

            Tables\Columns\TextColumn::make('expires_at')
                ->label('Bitiş')
                ->dateTime('d M Y H:i')
                ->sortable()
                ->color(fn ($record) => 
                    $record->expires_at?->isPast() 
                        ? 'danger' 
                        : 'success'
                )
                ->icon('heroicon-o-calendar'),

            Tables\Columns\IconColumn::make('is_active')
                ->label('Durum')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger'),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            SelectFilter::make('type')
                ->label('Kupon Tipi')
                ->options([
                    'percentage' => 'Yüzde İndirim',
                    'fixed' => 'Sabit İndirim',
                ]),

            SelectFilter::make('is_active')
                ->label('Durum')
                ->options([
                    '1' => 'Aktif',
                    '0' => 'Pasif',
                ]),

            Filter::make('expires_at')
                ->label('Süresi Dolmuş')
                ->query(fn (Builder $query) => 
                    $query->where('expires_at', '<', now())
                ),

            Filter::make('active_coupons')
                ->label('Aktif Kuponlar')
                ->query(fn (Builder $query) => 
                    $query->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        })
                        ->where(function ($query) {
                            $query->whereNull('usage_limit')
                                ->orWhere('used_count', '<', DB::raw('usage_limit'));
                        })
                ),
        ])
        ->actions([
            \Filament\Actions\ActionGroup::make([
                \Filament\Actions\ViewAction::make()
                    ->label('Görüntüle'),

                \Filament\Actions\EditAction::make()
                    ->label('Düzenle'),

                \Filament\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->is_active ? 'Pasif Yap' : 'Aktif Yap')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),

                \Filament\Actions\DeleteAction::make()
                    ->label('Sil'),
            ]),
        ])
        ->bulkActions([
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\BulkAction::make('activate')
                    ->label('Aktif Yap')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn ($records) => $records->each->update(['is_active' => true]))
                    ->deselectRecordsAfterCompletion(),

                \Filament\Actions\BulkAction::make('deactivate')
                    ->label('Pasif Yap')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($records) => $records->each->update(['is_active' => false]))
                    ->deselectRecordsAfterCompletion(),

                \Filament\Actions\DeleteBulkAction::make()
                    ->label('Toplu Sil'),
            ]),
        ])
        ->emptyStateIcon('heroicon-o-ticket')
        ->emptyStateHeading('Henüz Kupon Oluşturulmadı')
        ->emptyStateDescription('Yeni bir kupon oluşturmak için "Oluştur" butonuna tıklayın.')
        ->emptyStateActions([
            \Filament\Actions\CreateAction::make()
                ->label('Kupon Oluştur'),
        ]);
}


    // Resource sınıfınıza ekleyin
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 100 ? 'success' : 'info';
    }
// Resource sınıfının içine ekleyebileceğiniz ek özellikler:

// protected function getNavigationIcon(): ?string
// {
//     return 'heroicon-o-ticket';
// }

// protected function getNavigationBadge(): ?string
// {
//     return static::getModel()::where('is_active', true)->count();
// }

public static function getNavigationLabel(): string
{
    return 'Kuponlar';
}

public static function getNavigationGroup(): ?string
{
    return 'Pazarlama';
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'view' => Pages\ViewCoupon::route('/{record}'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
