<?php

namespace App\Exceptions;

class NotFoundException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Not Found') ;
        parent::__construct($this->message, 404);
    }
}
