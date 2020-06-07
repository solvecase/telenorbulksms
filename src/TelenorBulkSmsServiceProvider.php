<?php

namespace SolveCase\TelenorBulkSms;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client as HttpClient;
use SolveCase\TelenorBulkSms\Console\Commands\AuthorizeTelenorCommand;
use Illuminate\Support\Facades\Notification;
use SolveCase\TelenorBulkSms\TelenorSmsClient;
use SolveCase\TelenorBulkSms\TelenorSmsChannel;

class TelenorBulkSmsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'SolveCase');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'SolveCase');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->app->when(TelenorSmsChannel::class)
            ->needs(TelenorSmsClient::class)
            ->give(function() {
                return new TelenorSmsClient(new HttpClient([ 'base_uri' => config('telenorbulksms.base_url')]));
            });

        Route::middleware(['web'])
        ->group(function(){
            Route::get(config('telenorbulksms.sms.callback_url'), 'SolveCase\TelenorBulkSms\Http\Controllers\TelenorAuthCallbackController@callback')->name('telenorbulksms.callback');
        });        
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telenorbulksms.php', 'telenorbulksms');

        $this->commands[] = 'command.telenorbulksms.auth';
        $this->app->singleton('command.telenorbulksms.auth', function ($app) {
            return new AuthorizeTelenorCommand();
        });
        $this->commands($this->commands);

        $this->app->singleton('solvecase.telenorbulksms.console.kernel', function($app){
            $dispatcher = $app->make(\Illuminate\Contracts\Events\Dispatcher::class);
            return new \SolveCase\TelenorBulkSms\Console\Kernel($app, $dispatcher);
        });
        $this->app->make('solvecase.telenorbulksms.console.kernel');

        // Register the service the package provides.
        $this->app->singleton('telenorbulksms', function ($app) {
            return new TelenorBulkSms;
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('telenorsms', function ($app) {
                return new TelenorSmsChannel($app[TelenorSmsClient::class]);
            });
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['telenorbulksms'];
    }
    
    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/telenorbulksms.php' => config_path('telenorbulksms.php'),
        ], 'telenorbulksms.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/SolveCase'),
        ], 'telenorbulksms.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/SolveCase'),
        ], 'telenorbulksms.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/SolveCase'),
        ], 'telenorbulksms.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
