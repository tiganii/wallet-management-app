<?php

namespace App\Listeners;

use App\Enums\TransactionType;
use App\Events\WalletTransaction;
use App\Notifications\WalletCredited;
use App\Notifications\WalletDebited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWalletTransactionNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WalletTransaction $event): void
    {
         // Log::info("Customer Registered", $event->customer->toArray());
         $transaction =  $event->transaction;
         $user =  $event->user;
        if($transaction->type == TransactionType::CREDIT)
            $user->notify(new WalletCredited($transaction));
        if($transaction->type == TransactionType::DEBIT)
            $user->notify(new WalletDebited($transaction));
    }
}
