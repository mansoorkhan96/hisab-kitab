<?php

namespace App\Providers\Filament;

use App\Filament\Components\CottonCalculationInfolist;
use App\Filament\Components\WheatCalculationInfolist;
use App\Filament\Pages\Auth\Register;
use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Filament\Widgets\LoanWidget;
use Filament\Actions\CreateAction;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->profile(isSimple: false)
            ->login()
            ->registration(Register::class)
            ->sidebarFullyCollapsibleOnDesktop()
            ->spa()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ]);
    }

    public function boot()
    {
        CreateAction::configureUsing(function (CreateAction $action) {
            $action->icon(Heroicon::OutlinedPlusCircle);
        });

        Table::configureUsing(function (Table $table) {
            $table
                ->defaultKeySort(false)
                ->defaultSort('created_at', 'desc');
        });

        // TODO: not working
        // Stat::configureUsing(function (Stat $stat) {
        //     if (filled($stat->getColor())) {
        //         dd($stat->getColor());
        //     }
        // });

        // Need to register these otherwise these dont work in print view
        Livewire::component('app.filament.components.calculation-infolist', WheatCalculationInfolist::class);
        Livewire::component('app.filament.components.cotton-calculation-infolist', CottonCalculationInfolist::class);
        Livewire::component('app.filament.widgets.loan-widget', LoanWidget::class);

        // TODO: remove?
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn () => new HtmlString('<div class="ledgers-table">'),
            scopes: [EditCalculation::class],
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_END,
            fn () => new HtmlString('</div>'),
            scopes: [EditCalculation::class],
        );
    }
}
