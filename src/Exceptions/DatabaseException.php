<?php

namespace OtherCode\Database\Exceptions;

/**
 * Class DatabaseException
 * @package OtherCode\Database\Exceptions
 */
class DatabaseException extends \Exception
{
    /**
     * DatabaseException constructor.
     * @param string $message
     * @param int $code
     * @param \Throwable $previous Previous exception
     */
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code = 0, $previous);

    }
}