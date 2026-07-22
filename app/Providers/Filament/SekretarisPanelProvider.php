<?php

namespace App\Providers\Filament;

use App\Filament\Sekretaris\Widgets\PendingOrdersByProductTable;
use App\Filament\Sekretaris\Widgets\ProductStockTable;
use App\Filament\Sekretaris\Widgets\SecretaryOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SekretarisPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('sekretaris')
            ->path('sekretaris')
            ->brandName('Sekretaris / Secretary')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Sekretaris/Resources'), for: 'App\Filament\Sekretaris\Resources')
            ->discoverPages(in: app_path('Filament/Sekretaris/Pages'), for: 'App\Filament\Sekretaris\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Sekretaris/Widgets'), for: 'App\Filament\Sekretaris\Widgets')
            ->widgets([
                AccountWidget::class,
                SecretaryOverviewWidget::class,
                PendingOrdersByProductTable::class,
                ProductStockTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
