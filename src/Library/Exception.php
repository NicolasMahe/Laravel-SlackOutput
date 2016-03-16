<?php

namespace NicolasMahe\SlackOutput\Library;

use Illuminate\Support\Facades\Artisan;
use Exception as E;
use Illuminate\Support\Facades\Request;
use NicolasMahe\SlackOutput\Helper\ExceptionHelper;

class Exception
{

    /**
     * Report an exception to slack
     *
     * @param E $e
     */
    static public function output(E $e, $channel)
    {
        Artisan::queue('slack:post', [
            'to'      => $channel,
            'attach'  => self::exceptionToSlackAttach($e),
            'message' => "Thrown exception"
        ]);
    }


    /**
     * Transform an exception to attachment array for slack post
     *
     * @param E $e
     *
     * @return array
     */
    static protected function exceptionToSlackAttach(E $e)
    {
        $fields = [ ];

        $addToField = function ($name, $value, $short = false) use (&$fields) {
            if ( ! empty( $value )) {
                $fields[] = [
                    "title" => $name,
                    "value" => $value,
                    "short" => $short
                ];
            }
        };

        $addToField("Exception", get_class($e), true);
        $addToField("Hash", ExceptionHelper::hash($e), true);
        $addToField("Http code", ExceptionHelper::statusCode($e), true);
        $addToField("Code", $e->getCode(), true);
        $addToField("File", $e->getFile(), true);
        $addToField("Line", $e->getLine(), true);
        $addToField("Request url", Request::url(), true);
        $addToField("Request method", Request::method(), true);
        $addToField("Request param", json_encode(Request::all()), true);

        return [
            "color"    => "danger",
            "title"    => $e->getMessage(),
            "fallback" => ! empty( $e->getMessage() ) ? $e->getMessage() : get_class($e),
            "fields"   => $fields,
            "text"     => $e->getTraceAsString()
        ];
    }

}