<?php

namespace App\Exceptions;

use Exception;


class ApplicationException extends Exception
{
    
    protected $statusCode;
    
    public function __construct($message = null, $statusCode = null)
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(){
        return $this->statusCode;   
    }
}
