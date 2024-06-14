<?php

namespace App\Exceptions;


class InsufficientBalanceException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Insufficient Balance') ;
        parent::__construct($this->message, 422);
    }
}
