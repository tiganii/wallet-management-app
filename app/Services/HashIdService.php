<?php

namespace App\Services;

use Exception;
use Hashids\Hashids;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HashIdService
{
    public function __construct(
        public $hashIds = new Hashids("Wallet Hash secret", 10),
    ) {
    }

    public function encode($id)
    {
        return $this->hashIds->encode($id);
    }

    public function decode($hashId)
    {
        try {
            if (is_int($hashId))
                return $hashId;
            return $this->hashIds->decode($hashId)[0];
        } catch (Exception $e) {
            throw new NotFoundHttpException('Object not found', null, 404);
        }
    }
}
