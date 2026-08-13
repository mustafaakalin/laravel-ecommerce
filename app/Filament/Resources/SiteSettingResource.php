<?php

namespace App\Filament\Resources;

use BackedEnum;
use App\Filament\Resources\SiteSettingResource\Pages;
use App\Filament\Resources\SiteSettingResource\RelationManagers;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\IconPosition;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $recordTitleAttribute = 'site_name';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Temel Site Bilgileri')
                    ->description('Sitenizin temel bilgilerini buradan yönetebilirsiniz')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Site Adı')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Örn: Şirket İsmi')
                            ->prefixIcon('heroicon-m-building-storefront'),

                        Forms\Components\TextInput::make('site_slogan')
                            ->label('Slogan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Örn: Sizin için en iyisi')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('site_address')
                            ->label('Adres')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Adres')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('google_maps_embed')
                            ->label('Google Harita Gömme URL')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12684602.000875097!2d35.12932955000001!3d39.08764590000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b0155c964f2671%3A0x40d9dbd42a625f2a!2zVMO8cmtpeWU!5e0!3m2!1sen!2str!4v1734697109719!5m2!1sen!2str')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_whatsapp_group')
                            ->label('WhatsApp Grup Linki')
                            ->maxLength(255)
                            ->placeholder('https://chat.whatsapp.com/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_whatsapp_channel')
                            ->label('WhatsApp Kanal Linki')
                            ->maxLength(255)
                            ->placeholder('https://whatsapp.com/channel/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_telegram_group')
                            ->label('Telegram Grup Linki')
                            ->maxLength(255)
                            ->placeholder('https://t.me/joinchat/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_telegram_channel')
                            ->label('Telegram Kanal Linki')
                            ->maxLength(255)
                            ->placeholder('https://t.me/kanaladi')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_facebook_group')
                            ->label('Facebook Grup Linki')
                            ->maxLength(255)
                            ->placeholder('https://www.facebook.com/groups/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_facebook_page')
                            ->label('Facebook Sayfa Linki')
                            ->maxLength(255)
                            ->placeholder('https://www.facebook.com/sayfaadi')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_reddit_community')
                            ->label('Reddit Topluluğu Linki')
                            ->maxLength(255)
                            ->placeholder('https://www.reddit.com/r/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        Forms\Components\TextInput::make('social_instagram_broadcast_channnel')
                            ->label('Instagram Yayın Kanalı Linki')
                            ->maxLength(255)
                            ->placeholder('https://www.instagram.com/...')
                            ->prefixIcon('heroicon-m-megaphone'),

                        FileUpload::make('site_logo')
                            ->label('Logo')
                            ->image()
                            ->imageEditor()
                            ->directory('site-assets')
                            ->maxSize(1024)
                            ->helperText('Önerilen boyut: 200x60px, Maksimum boyut: 1MB')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('site_description')
                            ->label('Site Açıklaması')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->placeholder('Sitenizin kısa açıklaması...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('İletişim Bilgileri')
                    ->description('İletişim kanallarınızı buradan yönetebilirsiniz')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\TextInput::make('site_phone')
                            ->label('Telefon')
                            ->tel()
                            ->prefix('+90')
                            ->mask('999 999 99 99')
                            ->placeholder('5XX XXX XX XX')
                            ->prefixIcon('heroicon-m-phone'),

                        Forms\Components\TextInput::make('site_mail')
                            ->label('E-posta')
                            ->email()
                            ->placeholder('ornek@sirket.com')
                            ->prefixIcon('heroicon-m-envelope'),
                    ])
                    ->columns(2),



                Section::make('ETBIS Bilgileri')
                    ->description('ELEKTRONİK TİCARET DAİRESİ BAŞKANLIĞI BILGILERI')
                    ->icon('heroicon-o-qr-code')
                    ->schema([
                        FileUpload::make('site_etbis_qr')
                            ->label('ETBIS QR kod resmi')
                            ->image()
                            ->imageEditor()
                            ->directory('site-assets')
                            ->maxSize(1024)
                            ->helperText('Önerilen boyut: 200x60px, Maksimum boyut: 1MB'),

                        Forms\Components\TextInput::make('site_etbis_link')
                            ->label('ETBIS bağlantı adresi')
                            ->url()
                            ->placeholder('https://etbis.eticaret.gov.tr/sitedogrulama/8196FDEF645148A88D019AF721915768')
                            ->prefixIcon('heroicon-m-envelope'),

                    ])
                    ->columns(2),



                Section::make('Kargo Ücreti ?')
                    ->description('Sepetteki ürünlerin toplamına eklenir. 0-9999')
                    ->icon('heroicon-o-inbox-stack')
                    ->schema([
                        Forms\Components\TextInput::make('site_shipment_price')
                            ->label('Kargo Ücret Tutarı')
                            ->integer()
                            ->minValue(0)
                            ->maxValue(9999)
                            ->placeholder('50')
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-m-archive-box-arrow-down'),

                    ])
                    ->columns(2),


                Section::make('Sosyal Medya Bağlantıları')
                    ->description('Sosyal medya hesaplarınızı buradan yönetebilirsiniz')
                    ->icon('heroicon-o-share')
                    ->schema([
                        Forms\Components\TextInput::make('social_instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->prefixIcon('heroicon-m-camera')
                            ->placeholder('kullaniciadi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('site_facebook')
                            ->label('Facebook')
                            ->prefix('facebook.com/')
                            ->prefixIcon('heroicon-m-face-smile')
                            ->placeholder('sayfaadi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('site_youtube')
                            ->label('Youtube')
                            ->prefix('@')
                            ->prefixIcon('heroicon-m-play')
                            ->placeholder('kanaladi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('site_tiktok')
                            ->label('TikTok')
                            ->prefix('@')
                            ->prefixIcon('heroicon-m-musical-note')
                            ->placeholder('kullaniciadi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('site_linkedin')
                            ->label('LinkedIn')
                            ->prefix('linkedin.com/company/')
                            ->prefixIcon('heroicon-m-briefcase')
                            ->placeholder('sirketadi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),

                        Forms\Components\TextInput::make('site_x')
                            ->label('X (Twitter)')
                            ->prefix('@')
                            ->prefixIcon('heroicon-m-chat-bubble-bottom-center-text')
                            ->placeholder('kullaniciadi')
                            ->url()
                            ->suffixAction(
                                \Filament\Actions\Action::make('visit')
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->url(fn($state) => $state, true)
                                    ->visible(fn($state) => filled($state))
                            ),
                    ])
                    ->columns(2),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('site_logo')
                    ->label('Logo')
                    ->circular(false)
                    ->width(60)
                    ->height(60),

                Tables\Columns\TextColumn::make('site_name')
                    ->label('Site Adı')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->site_slogan)
                    ->wrap()
                    ->icon('heroicon-m-building-storefront'),

                Tables\Columns\TextColumn::make('site_description')
                    ->label('Açıklama')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function ($record): ?string {
                        if (strlen($record->site_description) > 50) {
                            return $record->site_description;
                        }
                        return null;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('site_phone')
                    ->label('İletişim')
                    ->searchable()
                    ->icon('heroicon-m-phone')
                    ->description(fn($record) => $record->site_mail)
                    ->copyable()
                    ->copyMessage('Telefon kopyalandı')
                    ->copyMessageDuration(1500),

                Tables\Columns\ViewColumn::make('social_media')
                    ->label('Sosyal Medya')
                    ->view('tables.columns.social-media-links'),
                // Bu view'i oluşturmanız gerekiyor - örnek aşağıda

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->color('gray'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\ViewAction::make()
                        ->label('Görüntüle')
                        ->icon('heroicon-m-eye'),

                    \Filament\Actions\EditAction::make()
                        ->label('Düzenle')
                        ->icon('heroicon-m-pencil'),

                    \Filament\Actions\Action::make('visit_site')
                        ->label('Siteyi Ziyaret Et')
                        ->icon('heroicon-m-globe-alt')
                        ->url(fn($record) => '//', true)
                        ->color('success'),
                ]),
            ])
            ->bulkActions([])  // Site ayarları için bulk actions gerekli değil
            ->emptyStateIcon('heroicon-o-cog-6-tooth')
            ->emptyStateHeading('Henüz Site Ayarı Oluşturulmamış')
            ->emptyStateDescription('Site ayarlarınızı buradan yönetebilirsiniz.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('Ayarları Oluştur'),
            ]);
    }



    // Resource sınıfına eklenecek özellikler


    public static function getNavigationLabel(): string
    {
        return 'Site Ayarları';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistem';
    }

    public static function getModelLabel(): string
    {
        return 'Site Ayarı';
    }

    protected static ?string $navigationBadge = '⚙️';

    protected static ?int $navigationSort = 1;
    // Performans için
    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
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
            'index' => Pages\ListSiteSettings::route('/'),
            // 'create' => Pages\CreateSiteSetting::route('/create'),
            'view' => Pages\ViewSiteSetting::route('/{record}'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}
