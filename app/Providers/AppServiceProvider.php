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
        //
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
