# Laravel SlackOutput

Sends messages to [Slack](https://slack.com) with your [Laravel](https://laravel.com) application.

This package provides:

* ## Post Command
	Send message to slack with a Laravel command.
	
* ## Stats Command
	Send stats about your Laravel app with this customizable command.
	
	![Stats on Slack](https://raw.githubusercontent.com/NicolasMahe/Laravel-SlackOutput/master/screenshots/stats.png)
	
* ## Exceptions handler
	Output to Slack useful information about exceptions when they occurred.
	
	![Exception on Slack](https://raw.githubusercontent.com/NicolasMahe/Laravel-SlackOutput/master/screenshots/exception.png)
	
* ## Failed jobs handler
	Get alerted when a job failed.
	
	![Job failed on Slack](https://raw.githubusercontent.com/NicolasMahe/Laravel-SlackOutput/master/screenshots/jobOutput.png)
	
* ## Scheduled commands reporting
	Keep an eye on the result of your scheduled commands.
	
	![Scheduled command on Slack](https://raw.githubusercontent.com/NicolasMahe/Laravel-SlackOutput/master/screenshots/scheduledCommand.png)
	

# Requirements

* Laravel 5.1 or greater
* PHP 5.5.9 or greater

# Installation

You can install the package using the [Composer](https://getcomposer.org/) package manager. You can install it by running this command in your project root:

```sh
composer require nicolasmahe/laravel-slack-output
```

You need to include the service provider and the facade in your Laravel app.

Add the service provider to the `providers` array in `config/app.php`:

```php
'providers' => [
  ...
  NicolasMahe\SlackOutput\ServiceProvider::class,
],
```

and then add the facade to your `aliases` array:

```php
'aliases' => [
  ...
  'SlackOutput' => NicolasMahe\SlackOutput\Facade\SlackOutput::class,
],
```

Publish the configuration file with:

```sh
php artisan vendor:publish --provider="NicolasMahe\SlackOutput\ServiceProvider"
```


You need to add the webhook URL to the configuration file in order for the package to post to Slack.
[Create an incoming webhook](https://my.slack.com/services/new/incoming-webhook) on your Slack account.
Copy the webhook url and open `config/slack-output.php` and set the webhook url to `endpoint`.

If `null` is set for any, the package will fall back on the default settings set by the webhook.

# Usage

## Post Command

The command `slack:post` posts message to Slack. It can take as arguments:

* `message`: the message to send
* `to`: the channel or person to post to
* `attach`: the attachment payload

You can find information about the attach argument here: https://api.slack.com/docs/attachments

You can call it by the running the command:

```sh
php artisan slack:post "Hello, I'm a bot" @nico
```

You can also call it in your Laravel app:

```php
Artisan::queue('slack:post', [
  'to' => "#api-output",
  'attach' => $someAttachment,
  'message' => "Hello, I'm a bot"
]);
```
Note the `Artisan::queue`, the command will be executed in background and will not block the current request.

## Stats command

The command `slack:stats` send useful stats about your app to slack.

You need to configure this command by setting in `config/slack-output.php` the Eloquent classes and dates you prefer.

You can add constraints to the classes to limit the number of counted data.

```php
'classes' => [
	  \App\Models\User::class => [
		  'is_active' => true //optional constraint
	  ]
],
```
	
The dates array is the form `'name of the date' => Carbon::instance()`. Like:

```php
'dates' => [
	'yesterday' => \Carbon\Carbon::yesterday(),
	'last week' => \Carbon\Carbon::today()->subWeek(1)
]
```

To schedule this command every day, simple add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
  $schedule->command('slack:stats')->daily()
}
```

## Exceptions handler

To report useful exception to Slack, open `app/Exceptions/Handler.php`, and transform it like:

```php
use NicolasMahe\SlackOutput\Facade\SlackOutput;

...

public function report(Exception $e)
{
  if ($this->shouldReport($e)) {
    SlackOutput::exception($e);
  }

  parent::report($e);
}
```

This will only reports exceptions that are not in the `$dontReport` array in the same file. 


## Failed jobs handler

To report failed jobs to Slack, open `app/Providers/AppServiceProvider.php`, and transform it like:

```php
use NicolasMahe\SlackOutput\Facade\SlackOutput;

...

public function boot()
{
  Queue::failing(function (JobFailed $job) {
    SlackOutput::jobFailed($job);
  });
}
```


## Scheduled commands reporting

To report the output of scheduled commands to Slack, open `app/Console/Kernel.php`, and transform it like:

```php
use NicolasMahe\SlackOutput\Facade\SlackOutput;

...

protected function schedule(Schedule $schedule)
{
  SlackOutput::scheduledCommand(
    $schedule->command('db:backup-auto')->daily()
  );
}
```


# Contributing

If you have problems, found a bug or have a feature suggestion, please add an issue on GitHub. Pull requests are also welcomed!

# License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
