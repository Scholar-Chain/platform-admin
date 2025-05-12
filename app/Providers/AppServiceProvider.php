<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\EditAction as TableEditAction;
use Filament\Tables\Actions\ViewAction as TableViewAction;
use Filament\Tables\Actions\CreateAction as TableCreateAction;
use Filament\Tables\Actions\DeleteAction as TablesDeleteAction;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        \Filament\Http\Responses\Auth\Contracts\LoginResponse::class => \App\Http\Responses\LoginResponse::class,
        \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class => \App\Http\Responses\LogoutResponse::class,
        \Filament\Http\Responses\Auth\Contracts\RegistrationResponse::class => \App\Http\Responses\RegisterResponse::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
        $this->filamentSettings();
    }

    private function configureVite(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }

    private function configureUrl(): void
    {
        if ($this->app->isProduction()) URL::forceScheme('https');
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict();

        Model::unguard();
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->isProduction(),
        );
    }

    private function filamentSettings(): void
    {
        /*
        * Disable create another record
        */
        CreateRecord::disableCreateAnother();
        CreateAction::configureUsing(fn(CreateAction $action) => $action->createAnother(false));
        TableCreateAction::configureUsing(fn(TableCreateAction $action) => $action->createAnother(false));

        /*
        * Change Default Action Modal Behavior
        */
        Action::configureUsing(fn(Action $action) => $action->slideOver());
        CreateAction::configureUsing(fn(CreateAction $action) => $action->slideOver());
        TableCreateAction::configureUsing(fn(TableCreateAction $action) => $action->slideOver());
        EditAction::configureUsing(fn(EditAction $action) => $action->slideOver());
        TableEditAction::configureUsing(fn(TableEditAction $action) => $action->slideOver()->tooltip(trans('filament-actions::edit.single.label'))->iconButton());
        TableViewAction::configureUsing(fn(TableViewAction $action) => $action->tooltip(trans('filament-actions::view.single.label'))->iconButton());
        TablesDeleteAction::configureUsing(fn(TablesDeleteAction $action) => $action->tooltip(trans('filament-actions::delete.single.label'))->iconButton());

        /*
        * Form Custom Settings
        */
        Section::configureUsing(fn(Section $section) => $section->columnSpanFull());
        Textarea::configureUsing(fn(Textarea $textarea) => $textarea->rows(5)->columnSpanFull());
        Radio::configureUsing(fn(Radio $radio) => $radio->inline()->inlineLabel(false));

        /*
        * Filament Default Icon
        */
        FilamentIcon::register([
            'panels::sidebar.collapse-button' => 'heroicon-o-bars-3-bottom-right',
            'panels::sidebar.expand-button' => 'heroicon-o-bars-3-bottom-left',
            'panels::pages.dashboard.navigation-item' => 'heroicon-o-chart-bar',
        ]);
    }
}
