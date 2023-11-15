<?php

namespace MeSomb\Exception;

use Exception;

class ServerException extends Exception
{
    protected $code;

    /**
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct($message, $code, $previous = null)
    {
        $this->code = $code;

        parent::__construct($message, 0, $previous);
    }
}