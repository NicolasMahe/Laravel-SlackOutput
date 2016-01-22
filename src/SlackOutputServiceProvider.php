<?php

namespace NicolasMahe\SlackOutput;

use Illuminate\Support\ServiceProvider;

class SlackOutputServiceProvider extends ServiceProvider
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
    $this->publishes([__DIR__ . '/Config/SlackOutput.php' => config_path('slack-output.php')]);

    //command
    $this->commands(
      Command\SlackPost::class
    );
  }


  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . '/Config/SlackOutput.php', 'slack-output');

    $this->app->singleton(SlackOutput::class, function () {
      return new SlackOutput(config('slack-output'));
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
      SlackOutput::class
    ];
  }

}
