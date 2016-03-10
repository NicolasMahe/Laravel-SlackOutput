<?php

namespace NicolasMahe\SlackOutput;

use Illuminate\Support\ServiceProvider as ServiceProviderParent;

class ServiceProvider extends ServiceProviderParent
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //config
        if (class_exists('Illuminate\Foundation\Application', false)) {
            $this->publishes([ __DIR__ . '/config.php' => config_path('slack-output.php') ]);
        }

        //command
        $this->commands(Command\SlackPost::class, Command\SlackStats::class);
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'slack-output');

        $this->app->singleton(Service::class, function () {
            return new Service(config('slack-output'));
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Service::class
        ];
    }

}
