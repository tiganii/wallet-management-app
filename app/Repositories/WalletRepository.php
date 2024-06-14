<?php

namespace App\Repositories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnsupportedTransactionException;
use App\Interfaces\WalletRepositoryInterface;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Cache;

class WalletRepository implements WalletRepositoryInterface
{
    /**
     * Creating transaction for user wallet, the transaction type can be either credit to card, or debit from card
     * @param   App\Models\Wallet $wallet
     * @param   App\Enums\TransactionType $type
     * @param   float $amount
     * @param   string $paymentMethod
     * @param   string $notes
     * @return  App\Models\WalletTransaction
     */
    public function createTransaction(Wallet $wallet, TransactionType $type, float $amount, string $paymentMethod, string $notes = null) : WalletTransaction
    {
        // If the transaction type credit, always charge the wallet.
        if ($type == TransactionType::CREDIT) {
            // charge wallet
            $wallet->amount += $amount;
            $wallet->save();
            // create transaction
            $transaction = $wallet->transactions()->create([
                'type' => TransactionType::CREDIT,
                'amount' => $amount,
                'new_balance' =>  $wallet->amount,
                'status' => TransactionStatus::SUCCUSS,
                'payment_method' => $paymentMethod,
                'notes'=>$notes
            ]);

        // If the transaction type debit, we need to check for sufficient balance
        } elseif ($type == TransactionType::DEBIT) {
            // If the wallet has enough balance do debit from wallet charge and create the transaction
            if ($wallet->amount > $amount) {
                // debit the wallet
                $wallet->amount -= $amount;
                $wallet->save();
                // create transaction
                $transaction = $wallet->transactions()->create([
                    'type' => TransactionType::DEBIT,
                    'amount' => $amount,
                    'new_balance' =>  $wallet->amount,
                    'status' => TransactionStatus::SUCCUSS,
                    'payment_method' => $paymentMethod,
                    'notes'=>$notes
                ]);

            // If the wallet has not enough balance create unsuccessful transaction and throw InsufficientBalanceException
            } else {
                $transaction = $wallet->transactions()->create([
                    'type' => TransactionType::DEBIT,
                    'amount' => $amount,
                    'new_balance' =>  $wallet->amount,
                    'status' => TransactionStatus::FAILED,
                    'payment_method' => $paymentMethod,
                    'notes'=>'Insufficient Balance'
                ]);
                throw new InsufficientBalanceException();
            }

        // If it is neither credit nor debit throw UnsupportedTransactionException
        }
        // Forget transaction cache of the user.
        Cache::forget('transactions_'.$wallet->user->id);
        // return response
        return $transaction;
    }
}
