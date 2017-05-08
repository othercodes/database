<?php

namespace OtherCode\Database\Exceptions;

/**
 * Class QueryException
 * @package OtherCode\Database\Exceptions
 */
class QueryException extends \Exception
{
    /**
     * QueryException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable $previous Previous exception
     */
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code = 0, $previous);

    }
}