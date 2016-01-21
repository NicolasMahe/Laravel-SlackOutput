<?php

namespace NicolasMahe\SlackOutput\Facade;

use Illuminate\Support\Facades\Facade;

use NicolasMahe\SlackOutput\SlackOutput as SlackOutput2;

class SlackOutput extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor() { return SlackOutput2::class; }

}