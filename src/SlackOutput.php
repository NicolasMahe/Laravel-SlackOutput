<?php

namespace NicolasMahe\SlackOutput;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Queue\Events\JobFailed;
use Exception;

class SlackOutput
{
  /**
   * Send to slack the results of the scheduled command.
   *
   * @param  Event $event
   * @return $this|void
   */
  public function scheduleCommand(Event $event)
  {
    $eventCommand = explode("'artisan' ", $event->command)[1];

    $event->sendOutputTo(base_path() . '/storage/logs/'.$eventCommand.'.txt');

    if (is_null($event->output)) {
      return;
    }

    return $event->then(function () use ($event, $eventCommand) {
      $message = file_get_contents($event->output);

      Artisan::call('slack:post', [
        'to' => '#api-command',
        'attach' => [
          'title' => $eventCommand,
          'text'  => $message
        ]
      ]);
    });
  }


  /**
   * Output the failed job to slack
   *
   * @param JobFailed $event
   */
  public function jobFailed(JobFailed $event)
  {
    /*
    $fields = array();
    foreach($event->data["data"] as $key => $value) {
      if (in_array($key, ["callback", "token", "command"])) {
        break;
      }
      if (!empty($value)) {
        if (is_array($value)) {
          $value = json_encode($value, JSON_PRETTY_PRINT);
        }
        $fields[] = [
          'title' => $key,
          'value' => $value
        ];
      }
    }*/

    $message = "Job '".$event->job->getName()."' failed.\n";
    $message .= json_encode($event->data['data']['command'], JSON_PRETTY_PRINT);

    Artisan::call('slack:post', [
      'to' => '#api-failed-jobs',
      'message' => $message
    ]);
  }


  /**
   * Report exception to slack
   *
   * @param $e
   */
  public function exceptionReport(Exception $e) {
    Artisan::queue('slack:post', [
      'to' => '#api-exception',
      'attach' => $this->exceptionToSlackAttach($e)
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

    $addToField("exception",  get_class($e),                      true);
    $addToField("hash",       ExceptionHelper::hash($e),          true);
    $addToField("http code",  ExceptionHelper::statusCode($e),    true);
    $addToField("code",       $e->getCode(),                      true);
    $addToField("file",       $e->getFile(),                      true);
    $addToField("line",       $e->getLine(),                      true);
    $addToField("trace",      $e->getTraceAsString(),             false);

    return [
      "text"      => $e->getMessage(),
      "fallback"  => get_class($e),
      "fields"    => $fields
    ];
  }

}