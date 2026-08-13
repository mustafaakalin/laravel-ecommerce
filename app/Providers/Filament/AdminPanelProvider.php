<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\ProductStatsWidget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use DutchCodingCompany\FilamentSocialite\Provider;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Filament\Resources\UserResource\Widgets\UserWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\BrandResource\Widgets\BrandWidget;
use App\Filament\Resources\OrderResource\Widgets\OrderWidget;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use App\Filament\Resources\AddressResource\Widgets\AddressWidget;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use App\Filament\Resources\CartItemResource\Widgets\CartItemWidget;
use App\Filament\Resources\UserResource\Widgets\UserAnalyticsWidget;
use App\Filament\Resources\CampaignProductResource\Widgets\CampaignProductWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                ProductStatsWidget::class,
                // AddressWidget::class,
                // BrandWidget::class,
                // CampaignProductWidget::class,
                // CampaignWidget::class,
                // CartItemWidget::class,
                // CartWidget::class,
                // CategoryWidget::class,
                // CommentWidget::class,
                // CouponWidget::class,
                // LikeWidget::class,
                // OrderItemWidget::class,
                 OrderWidget::class,
                // PermissionWidget::class,
                // ProductAttributeWidget::class,
                // ProductAttributeValueWidget::class,
                // ProductImageWidget::class,
                // ProductWidget::class,
                // RoleWidget::class,
                // ShipmentWidget::class,
                // SoldoutWidget::class,
                // TestimonialWidget::class,
                // UserWidget::class,
                // UserAnalyticsWidget::class,

            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('AnaSayfa')
                    ->url('/', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-home')
                    ->group('HomePage')
                    ->sort(1),
                NavigationItem::make('Genel Bakış')
                    ->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                    ->url(fn (): string => Dashboard::getUrl())
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),
                // ...
            ])
            ->plugin(    
                FilamentSocialitePlugin::make()
                    // (required) Add providers corresponding with providers in `config/services.php`.
                    ->providers([
                        // Create a provider 'gitlab' corresponding to the Socialite driver with the same name.
                        // Provider::make('github')
                        // ->label('Github')
                        // ->icon('fab-github')
                        // ->scopes([
                        //     // Add scopes here.
                        //     'read:user',
                        //     'public_repo',
                        // ]),
                        Provider::make('google')
                        ->label('Google')
                        ->icon('fab-google')
                        ->scopes([
                            // Add scopes here.
                        ]),
                        // Provider::make('facebook')
                        // ->label('Facebook')
                        // ->icon('fab-facebook')
                        // ->scopes([
                        //     // Add scopes here.
                        // ]),
                        // Provider::make('instagram')
                        // ->label('Instagram')
                        // ->icon('fab-instagram')
                        // ->scopes([
                        //     // Add scopes here.
                        // ]),
                    ])
                    // (optional) Override the panel slug to be used in the oauth routes. Defaults to the panel ID.
                    ->slug('admin')
                    // (optional) Enable/disable registration of new (socialite-) users.
                    ->registration(true)
                    // (optional) Enable/disable registration of new (socialite-) users using a callback.
                    // In this example, a login flow can only continue if there exists a user (Authenticatable) already.
                    // ->registration(fn (string $provider, SocialiteUserContract $oauthUser, ?Authenticatable $user) => (bool) $user)
                    // (optional) Change the associated model class.
                    ->userModelClass(\App\Models\User::class)
                    // (optional) Change the associated socialite class (see below).
                    // ->socialiteUserModelClass(\App\Models\SocialiteUser::class)

                   

                    
            );
    }
}
