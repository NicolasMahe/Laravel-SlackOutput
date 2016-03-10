<?php

return [

    /*
    |-------------------------------------------------------------
    | Incoming webhook endpoint
    |-------------------------------------------------------------
    |
    | The endpoint which Slack generates when creating a
    | new incoming webhook. It will look something like
    | https://hooks.slack.com/services/XXXXXXXX/XXXXXXXX/XXXXXXXXXXXXXX
    |
    */

    'endpoint' => '',

    /*
    |-------------------------------------------------------------
    | Channels
    |-------------------------------------------------------------
    |
    | The channels we should post to. The channel can either be a
    | channel like #general, a private #group, or a @username. Set to
    | null to use the default set on the Slack webhook.
    |
    | The production and local are corresponding to the app environment.
    | You can add any app environment to this array.
    | The default (if not exist) will be the local.
    |
    */

    'channel' => [
        "production" => [
            "job_failed"        => "",
            "scheduled_command" => "",
            "exception"         => "",
            "stats"             => "",
        ],
        "local"      => [
            "job_failed"        => "",
            "scheduled_command" => "",
            "exception"         => "",
            "stats"             => "",
        ],
    ],

    /*
    |-------------------------------------------------------------
    | Default username
    |-------------------------------------------------------------
    |
    | The default username we should post as. Set to null to use
    | the default set on the Slack webhook
    |
    */

    'username' => '',

    /*
    |-------------------------------------------------------------
    | Default icon
    |-------------------------------------------------------------
    |
    | The default icon to use. This can either be a URL to an image or Slack
    | emoji like :ghost: or :heart_eyes:. Set to null to use the default
    | set on the Slack webhook
    |
    */

    'icon' => "",

    /*
    |-------------------------------------------------------------
    | Stats command
    |-------------------------------------------------------------
    |
    | Configuration for the stats command
    |
    */

    'stats' => [

        /*
        |-------------------------------------------------------------
        | Stats command - Classes
        |-------------------------------------------------------------
        |
        | Indicate the Eloquent classes you want the stats from.
        | (Optional) You can also pass an array of constraints to limit
        | the numbers of counted data.
        |
        | Example:
        | 'classes' => [
        |	  \App\Models\User::class => [
        |		  'is_active' => true //optional constraint
        |	  ]
        | ],
        |
        */

        'classes' => [
            \App\Models\User::class
        ],

        /*
        |-------------------------------------------------------------
        | Stats command - Dates
        |-------------------------------------------------------------
        |
        | Set the dates the stats will be counted from.
        |
        | The form is like :
        | 'dates' => [
         |   "date name" => Carbon_object
        | ]
        |
        | Example (default):
        | 'dates' => [
        | 	'yesterday' => \Carbon\Carbon::yesterday(),
        | 	'last week' => \Carbon\Carbon::today()->subWeek(1)
        | ],
        |
        */

        'dates' => [
            'yesterday' => \Carbon\Carbon::yesterday(),
            'last week' => \Carbon\Carbon::today()->subWeek(1)
        ],

    ],

];