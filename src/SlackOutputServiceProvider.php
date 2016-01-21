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
  protected $defer = false;


  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    //...
  }


  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton(SlackOutput::class, function () {
      return new SlackOutput();
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
