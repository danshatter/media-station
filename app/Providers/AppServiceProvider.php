<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        Password::defaults(function () {
            $rule = Password::min(8);
     
            return $this->app->isProduction() ? $rule->letters()
                                                    ->mixedCase()
                                                    ->numbers()
                                                    ->symbols()
                                            : $rule;
        });
    }
}
