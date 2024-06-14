<?php

use App\Enums\TransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\UnsupportedTransactionException;
use App\Models\Wallet;
use App\Repositories\WalletRepository;



beforeEach(function () {
        // $this->useRefreshDatabase();
        $this->mockWallet = Wallet::factory()->create(['amount' => 100]);
        $this->WalletRepository = new WalletRepository();
    });
    
    afterEach(function () {
        // Clean up after each test if needed
        // This can include rolling back transactions, resetting state, etc.
        // Again, this depends on your specific application needs.
    });
    
    it('creates a credit transaction', function () {
        $type = TransactionType::CREDIT;
        $amount = 50.0;
        $paymentMethod = 'Credit Card';
        $notes = 'Test credit transaction';
    
        $transaction = $this->WalletRepository->createTransaction($this->mockWallet, $type, $amount, $paymentMethod, $notes);
    
        expect($transaction->type)->toBe(TransactionType::CREDIT);
        expect($transaction->amount)->toBe($amount);
        // Additional assertions for new_balance, status, payment_method, etc.
        expect($this->mockWallet->amount)->toBe(100 + $amount); // Check if amount was updated correctly
    });
    
    it('creates a debit transaction with sufficient balance', function () {
        $type = TransactionType::DEBIT;
        $amount = 50.0;
        $paymentMethod = 'Debit Card';
        $notes = 'Test debit transaction';
    
        $transaction = $this->WalletRepository->createTransaction($this->mockWallet, $type, $amount, $paymentMethod, $notes);
    
        expect($transaction->type)->toBe(TransactionType::DEBIT);
        expect($transaction->amount)->toBe($amount);
        // Additional assertions for new_balance, status, payment_method, etc.
        expect($this->mockWallet->amount)->toBe(100 - $amount); // Check if amount was updated correctly
    });
    
    it('throws InsufficientBalanceException for debit transaction with insufficient balance', function () {
        $type = TransactionType::DEBIT;
        $amount = 150; // Attempting to debit more than balance
        $paymentMethod = 'Debit Card';
        $notes = 'Test debit transaction';
    
        $this->expectException(InsufficientBalanceException::class);
    
        $this->WalletRepository->createTransaction($this->mockWallet, $type, $amount, $paymentMethod, $notes);
    });
    
    