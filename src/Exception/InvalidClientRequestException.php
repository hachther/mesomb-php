<?php

namespace MeSomb\Exception;

use Exception;
use Throwable;

class InvalidClientRequestException extends Exception
{
    protected $code;

    public function __construct(string $message = "", string $code = "", ?Throwable $previous = null)
    {
        $this->code = $code;

        parent::__construct($message, 0, $previous);
    }
}
