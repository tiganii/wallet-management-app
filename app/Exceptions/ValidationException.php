<?php

namespace App\Exceptions;

class ValidationException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Validation Error') ;
        parent::__construct($this->message, 400);
    }
}