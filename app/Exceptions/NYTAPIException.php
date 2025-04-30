<?php

namespace App\Exceptions;

use Exception;

class NYTAPIException extends Exception
{
    public function __construct($message = "Error with NYT API", $code = 0)
    {
        parent::__construct($message, $code);
    }
}
