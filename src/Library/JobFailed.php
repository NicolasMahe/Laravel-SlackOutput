<?php

namespace NicolasMahe\SlackOutput\Library;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\Events\JobFailed as JB;

class JobFailed
{

    /**
     * Output a failed job to slack
     *
     * @param JB $event
     * @param    $channel
     */
    static public function output(JB $event, $channel)
    {
        $message = "Job '" . $event->job->getName() . "' failed.";
        Artisan::call('slack:post', [
            'to'      => $channel,
            'message' => $message
        ]);
    }

}