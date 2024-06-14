<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'throttle:10,1', // Add Rate limiter to avoid DDoS , only 10 request available per minute.
    ],
    function ($router) {

        Route::get('/', function () {
            return 'Welcome to Wallet Management System, Please Login to access this feature';
        });

        Route::group(
            [
                'middleware' => 'api',
                'prefix' => 'auth',
            ],
            function ($router) {
                Route::post('register', [AuthController::class, 'register'])->name('register');
                Route::post('login', [AuthController::class, 'login'])->name('login');
                Route::post('logout', [AuthController::class, 'logout'])->name('logout');
                Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
                Route::post('me', [AuthController::class, 'me'])->name('me');
            },
        );

        Route::group(
            [
                'middleware' => 'auth:api',
                'prefix' => 'wallet',
            ],
            function ($router) {
                Route::post('add-funds', [WalletController::class, 'addFunds'])->name('add-funds');
                Route::post('transfer', [WalletController::class, 'transfer'])->name('transfer');
                Route::get('transactions', [WalletController::class, 'transactions'])->name('transactions');
                Route::post('withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
                Route::get('transactions/pdf', [WalletController::class, 'generateTransactionsPDF'])->name('transactions-pdf');
                Route::get('transactions/downland/{filename}', [WalletController::class, 'downloadTransactionsPDF'])->name('transactions.download');
                Route::post('transfer/qr', [WalletController::class, 'generateTransfersQRCode'])->name('transfer-qr');
                Route::get('transfer/downland/{filename}', [WalletController::class, 'downloadTransfersQRCode'])->name('transfer.download');
            },
        );
    },
);
