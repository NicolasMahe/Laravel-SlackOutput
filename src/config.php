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
  | null to use the default set on the Slack webhook
  |
  */

  'channel' => [
    "job_failed"        => "",
    "scheduled_command" => "",
    "exception"         => "",
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


];