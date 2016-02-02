<?php

namespace NicolasMahe\SlackOutput;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Queue\Events\JobFailed;
use Exception;

class Service
{

  /**
   * The default channel to send job failed to
   *
   * @var string
   */
  protected $channel_job_failed;

  /**
   * The default channel to send scheduled command to
   *
   * @var string
   */
  protected $channel_scheduled_command;

  /**
   * The channel to send exception to
   *
   * @var string
   */
  protected $channel_exception;

  /**
   * SlackOutput constructor.
   *
   * @param array $config
   * @throws Exception
   */
  function __construct(array $config)
  {
    $env = app()->environment();
    $channel = $config["channel"]['local'];
    if (isset($config["channel"][$env])) {
      $channel = $config["channel"][$env];
    }

    //config
    $this->channel_job_failed         = $channel["job_failed"];
    $this->channel_scheduled_command  = $channel["scheduled_command"];
    $this->channel_exception          = $channel["exception"];
  }

  /**
   * Send to slack the results of a scheduled command.
   *
   * @todo: add success tag to adjust the color
   * @param  Event $event
   * @return $this|void
   */
  public function scheduledCommand(Event $event)
  {
    preg_match("/(artisan |'artisan' )(.*)/us", $event->command, $matches);
    $eventCommand = $matches[2];

    $event->sendOutputTo(base_path() . '/storage/logs/'.$eventCommand.'.txt');
    if (is_null($event->output)) {
      //if no output, don't send anything
      return;
    }

    return $event->then(function () use ($event, $eventCommand) {
      $message = file_get_contents($event->output);

      Artisan::call('slack:post', [
        'to' => $this->channel_scheduled_command,
        'attach' => [
          'color' => 'grey',
          'title' => $eventCommand,
          'text'  => $message
        ]
      ]);
    });
  }

  /**
   * Output a failed job to slack
   *
   * @param JobFailed $event
   */
  public function jobFailed(JobFailed $event)
  {
    $message = "Job '".$event->job->getName()."' failed.";
    Artisan::call('slack:post', [
      'to' => $this->channel_job_failed,
      'message' => $message
    ]);
  }

  /**
   * Report an exception to slack
   *
   * @param $e
   */
  public function exception(Exception $e) {
    Artisan::queue('slack:post', [
      'to' => $this->channel_exception,
      'attach' => $this->exceptionToSlackAttach($e),
      'message' => "Thrown exception"
    ]);
  }

  /**
   * Transform an exception to attachment array for slack post
   *
   * @param Exception $e
   * @return array
   */
  public function exceptionToSlackAttach(Exception $e) {
    $fields = [];

    $addToField = function($name, $value, $short = false) use (&$fields) {
      if (!empty($value)) {
        $fields[] = [
          "title" => $name,
          "value" => $value,
          "short" => $short
        ];
      }
    };

    $addToField("Exception",  get_class($e),                      true);
    $addToField("Hash",       ExceptionHelper::hash($e),          true);
    $addToField("Http code",  ExceptionHelper::statusCode($e),    true);
    $addToField("Code",       $e->getCode(),                      true);
    $addToField("File",       $e->getFile(),                      true);
    $addToField("Line",       $e->getLine(),                      true);

    return [
      "color"     => "danger",
      "title"     => $e->getMessage(),
      "fallback"  => !empty($e->getMessage()) ? $e->getMessage() : get_class($e),
      "fields"    => $fields,
      "text"      => $e->getTraceAsString()
    ];
  }

}