<?php

namespace Enzaime\Payment;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as LaravelProvider;

class ServiceProvider extends LaravelProvider
{
    protected $rootPath;

    /**
    * Register any application services.
    *
    * @return void
    */
    public function register()
    {
        $this->rootPath = realpath(__DIR__.'/../');
    }

    public function boot()
    {
        $this->loadMigrationsFrom($this->rootPath . '/database/migrations');
        $this->loadViewsFrom($this->rootPath . '/resources/views', PackageConst::VIEW_NAMESPACE);
        $this->loadRoutesFrom($this->rootPath . '/routes/api.php');
        $this->mergeConfigFrom($this->rootPath . '/config/payment.php', 'payment');

        // Register middleware
        // Route::aliasMiddleware('my-package.middleware_name', CustomMiddleware::class);

        $this->bindViewComposer();
        $this->publishAssets();
        $this->loadCommands();
    }

    private function bindViewComposer()
    {
        View::composer(PackageConst::VIEW_NAMESPACE . '::*', function($view) {
            $view->with('viewPath', PackageConst::VIEW_NAMESPACE . '::');
        });
    }

    private function publishAssets()
    {
        $this->publishes([
            $this->rootPath .'/resources/views' => resource_path('views/vendor/'. PackageConst::PACKAGE_NAME),
        ], 'views');
        
        $this->publishes([
            $this->rootPath .'/config/payment.php' => config_path('payment.php'),
        ], 'config');
    }

    private function loadCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
            ]);
        }
    }
}