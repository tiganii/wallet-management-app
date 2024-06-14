<?php

namespace App\Exceptions;


class UnsupportedTransactionException extends ApplicationException
{
    protected $message;
    public function __construct(string $message = null){
        $this->message = $message? $message : __('Unsupported Transaction') ;
        parent::__construct($this->message, 422);
    }
}
