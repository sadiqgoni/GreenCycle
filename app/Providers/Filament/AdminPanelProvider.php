<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\AdminRegistration;
use App\Filament\Household\Pages\HouseholdRegistration;
use App\Filament\Management\Widgets\Stats\StatsOverview;
use App\Filament\Widgets\Greencyclewidgets;
use App\Http\Middleware\RoleRedirect;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigationGroups([
                'Request Management',
                'Company Management',
                'Staff Management',
            ])
            ->registration(AdminRegistration::class)
            ->login()
            ->plugins([  FilamentApexChartsPlugin::make()])
            ->discoverResources(in: app_path('Filament/Company/Resources'), for: 'App\\Filament\\Company\\Resources')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Greencyclewidgets::class,
                StatsOverview::class
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
                RoleRedirect::class,
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');

    }
}
