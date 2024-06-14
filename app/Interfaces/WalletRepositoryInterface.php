<?php

namespace App\Interfaces;

use App\Enums\TransactionType;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function createTransaction(Wallet $wallet, TransactionType $type, float $amount, string $paymentMethod, string $notes = null);
}
