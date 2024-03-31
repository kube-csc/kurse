<?php

namespace App\Providers;

use App\Components\FlashMessages;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use FlashMessages;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('components.backend.flash-messages', function($view) {
            $messages = self::messages();
            return $view->with('messages', $messages);
        });
    }
}
