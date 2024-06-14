<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Events\WalletTransaction;
use App\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class WalletService
{

    public function __construct(
        private WalletRepositoryInterface $walletRepository,
        private UserService $userService
    ){}

    
    public function addFunds(int $userId, float $amount, string $paymentMethod){
        $user = $this->userService->get($userId);
        // create credit transaction of the amount received 
        $transaction = $this->walletRepository->createTransaction(
            $user->wallet,
            TransactionType::CREDIT,
            $amount,
            $paymentMethod,
            'Wallet funded by'.$paymentMethod
        );
        // Fire Wallet transaction event with user and transaction Info
        WalletTransaction::dispatch($user, $transaction);
        return $transaction;
    }

    public function transferFunds(int $senderId, int $recipientId, float $amount){
        // get sender user 
        $sender = $this->userService->get($senderId);
        // get recipient user 
        $recipient = $this->userService->get($recipientId);
        // create debit transaction for the sender 
        $senderTransaction = $this->walletRepository->createTransaction(
            $sender->wallet,
            TransactionType::DEBIT,
            $amount,
            'wallet',
            'Wallet Transfer to '.$recipient->name
        );
        // Fire Wallet transaction event with sender and transaction Info
        WalletTransaction::dispatch($sender, $senderTransaction);

         // create credit transaction for the recipient if debit transaction of sender successfully created
        $recipientTransaction = $this->walletRepository->createTransaction(
            $recipient->wallet,
            TransactionType::CREDIT,
            $amount,
            'wallet',
            'Wallet Transfer from '.$sender->name
        );
        // Fire Wallet transaction event with recipient and transaction Info
        WalletTransaction::dispatch($recipient, $recipientTransaction);

        return $senderTransaction;

    }

    public function getWalletTransactions(int $userId){
        // cache response for 30 minutes (it will be invalidated immediately if transaction happened)
        $user = $this->userService->get($userId);
        $transactions = Cache::remember('transactions_'.$userId, 60, function () use ($user) {
            return $user->wallet->transactions;
        });
        
        return $transactions;
    }

    public function withdrawFundsToBank(int $userId, float $amount, string $bank_account){
        $user = $this->userService->get($userId);
        $transaction = $this->walletRepository->createTransaction(
            $user->wallet,
            TransactionType::DEBIT,
            $amount,
            'bank_withdraw',
            'Withdraw to '.$bank_account
        );
        // Fire Wallet transaction event with user and transaction Info
        WalletTransaction::dispatch($user, $transaction);
        return $transaction;

    }

    public function generateTransactionsPDF(int $userId){
        $transactions = $this->getWalletTransactions($userId);
        $pdf = \PDF::loadView('transactions.pdf', ['transactions'=>$transactions]);
        $fileName = 'transactions_'. $userId. '_' . time() . '.pdf';
        // Save the PDF file to the storage disk storage
        Storage::disk('downloads')->put('transactions/'.$fileName, $pdf->output());
        // Generate the URL to access the PDF
        $url = route('transactions.download', Crypt::encrypt($fileName));
        // return $pdf->download('transactions.pdf');
        return  $url;
    }

    public function generateTransfersQRCode(int $senderId, int $recipientId, float $amount){
         // Define the URL for the payment POST request
         $url = route('transfer', [
            'recipient_id' => $recipientId,
            'amount' => $amount
        ], true);
        $qrcode = \QrCode::format('png')->size(300)->generate($url);
        $fileName = 'transfer_'. $senderId. '_' . time() . '.png';
        // Save the QR Code file to the storage
        Storage::disk('downloads')->put('transfer/'.$fileName, $qrcode);
        // Generate the URL to access the PDF
        $url = route('transfer.download', Crypt::encrypt($fileName));
        // return $pdf->download('transactions.pdf');
        return  $url;
    }

}
