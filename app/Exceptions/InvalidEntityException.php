<?php

namespace App\Exceptions;


class InvalidEntityException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Invalid Entity ') ;
        parent::__construct($this->message, 401);
    }
}
