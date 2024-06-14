<?php

namespace App\Exceptions;

class ServiceUnavailableException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Service Unavailable') ;
        parent::__construct($this->message, 503);
    }
}