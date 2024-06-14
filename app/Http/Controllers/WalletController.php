<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFundsRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\Response;
use App\Http\Requests\TransactionHistoryRequest;
use App\Http\Requests\WithdrawRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class WalletController extends BaseController 
{
    public function __construct(
        public WalletService $walletService,
    ){}

    public function addFunds(AddFundsRequest $request){
        $user = auth()->user();
        $transaction = $this->walletService->addFunds(
            $user->id,
            $request->safe()->amount,
            $request->safe()->payment_method,
        );
    
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Funds Added Successfully to Wallet', 'new_balance' => $transaction->new_balance]);
    }

    public function transfer(TransferRequest $request){
        $user = auth()->user();
        $transaction = $this->walletService->transferFunds(
            $user->id,
            $request->safe()->recipient_id,
            $request->safe()->amount,
        );
    
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Funds Transferred Successfully', 'new_balance' => $transaction->new_balance]);
    }

    public function transactions(){
        $user = auth()->user();
        $transactions = $this->walletService->getWalletTransactions($user->id);
    
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Transactions Retrieved Successfully', 'transactions' => TransactionResource::collection($transactions)]);
    }

    public function withdraw(WithdrawRequest $request){
        $user = auth()->user();
        $transaction = $this->walletService->withdrawFundsToBank(
            $user->id,
            $request->safe()->amount,
            $request->safe()->bank_account,
        );
    
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'Funds Added Successfully to Wallet', 'new_balance' => $transaction->new_balance]);
    }

    public function generateTransactionsPDF(){
        $user = auth()->user();
        $pdfURL = $this->walletService->generateTransactionsPDF($user->id);
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'PDF Generated Successfully', 'pdf_url' => $pdfURL],JSON_UNESCAPED_SLASHES);
    }

    public function downloadTransactionsPDF($filename){
        return Storage::disk('downloads')->download('transactions/'.Crypt::decrypt($filename));
    }

    public function generateTransfersQRCode(TransferRequest $request){
        $user = auth()->user();
        $QRCodeURL = $this->walletService->generateTransfersQRCode(
            $user->id,
            $request->safe()->recipient_id,
            $request->safe()->amount,
        );
        return $this->sendResponse(Response::HTTP_OK, ['success'=>true,'message'=>'QR Code Generated Successfully', 'qr_code_url' => $QRCodeURL],JSON_UNESCAPED_SLASHES);
    }

    public function downloadTransfersQRCode($filename){
        return Storage::disk('downloads')->download('transfer/'.Crypt::decrypt($filename));
    }
}
