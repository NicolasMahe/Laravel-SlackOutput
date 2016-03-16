<?php

namespace NicolasMahe\SlackOutput\Library;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Event;

class ScheduledCommand
{

    /**
     * Send to slack the results of a scheduled command.
     *
     * @todo: add success tag to adjust the color
     *
     * @param  Event $event
     * @param        $channel
     *
     * @return void
     */
    static public function output(Event $event, $channel)
    {
        preg_match("/(artisan |'artisan' )(.*)/us", $event->command, $matches);
        $eventCommand = $matches[2];

        $event->sendOutputTo(base_path() . '/storage/logs/' . $eventCommand . '.txt');
        if (is_null($event->output)) {
            //if no output, don't send anything
            return;
        }

        $event->then(function () use ($event, $eventCommand, $channel) {
            $message = file_get_contents($event->output);

            Artisan::call('slack:post', [
                'to'     => $channel,
                'attach' => [
                    'color' => 'grey',
                    'title' => $eventCommand,
                    'text'  => $message
                ]
            ]);
        });
    }

}