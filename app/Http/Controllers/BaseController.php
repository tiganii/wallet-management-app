<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HashIdService;
use App\Traits\HasResponse;

class BaseController extends Controller {
    use HasResponse;

    public function __construct(
        public $hashIdService = new HashIdService(),
    ){}
}