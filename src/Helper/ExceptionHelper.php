<?php

namespace NicolasMahe\SlackOutput\Helper;

use Exception;

class ExceptionHelper
{

    /**
     * Return an array from an Exception
     *
     * @param Exception $e
     *
     * @return array
     */
    static function asArray(Exception $e)
    {
        return [
            'exception'   => get_class($e),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'hash'        => self::hash($e),
            'status_code' => self::statusCode($e)
        ];
    }


    /**
     * Get the http status code of an exception
     *
     * @param Exception $e
     *
     * @return int
     */
    static function statusCode(Exception $e)
    {
        return $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ? $e->getStatusCode() : 500;
    }


    /**
     * Get the hash code of an exception
     *
     * @param Exception $e
     *
     * @return null
     */
    static function hash(Exception $e)
    {
        return $e instanceof \App\Exceptions\JSONException ? $e->getHash() : null;
    }

}