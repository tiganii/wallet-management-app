<?php

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Testing\Fluent\AssertableJson;

it('assure user can add-funds to wallet', function () {
    
    $user = User::factory()->has(Wallet::factory(['amount'=>0]))->create();
    // make sure the user has 0 balance 
    expect($user->wallet->amount)->toBe(0.0);
    $response = $this->actingAs($user)->post('/api/v1/wallet/add-funds',[
        "amount" => 500,
        "payment_method" => "bank_transfer"
    ]);

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'new_balance']));
    // assure the response and the wallet reflects to funds added 
    expect($response->json('new_balance'))->toBe(500);
});

it('assure user can transfer fund', function () {
    
    $sender = User::factory()->has(Wallet::factory(['amount' => 200]))->create();
    $recipient = User::factory()->has(Wallet::factory(['amount' => 50]))->create();
    // make sure the user has 0 balance 

    $response = $this->actingAs($sender)->post('/api/v1/wallet/transfer',[
        "amount" => 100,
        "recipient_id" => $recipient->id
    ]);

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'new_balance']));
    // assure the response is 100 after transferring 100 from the sender wallet (was 200)
    expect($response->json('new_balance'))->toBe(100);
});

it('assure user can view transactions history', function () {
    
    $user = User::factory()->has(Wallet::factory(['amount'=>0]))->create();
    // make sure the user has 0 balance 
    expect($user->wallet->amount)->toBe(0.0);
    $response = $this->actingAs($user)->post('/api/v1/wallet/add-funds',[
        "amount" => 500,
        "payment_method" => "bank_transfer"
    ]);

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'new_balance']));
    // assure the response and the wallet reflects to funds added 
    expect($response->json('new_balance'))->toBe(500);

    $response = $this->actingAs($user)->get('/api/v1/wallet/transactions');
    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'transactions']));
    $transactions = $response->json('transactions'); 
    // at least transaction array has one element (fund transaction above)
    expect($transactions)->toBeArray()->not->toBeEmpty();

});

it('assure user can view withdraw fund to bank', function () {
    
    $user = User::factory()->has(Wallet::factory(['amount'=>150]))->create();
    // make sure the user has 100 wallet balance 
    expect($user->wallet->amount)->toBe(150.0);
    $response = $this->actingAs($user)->post('/api/v1/wallet/withdraw',[
        "amount" => 50,
        "bank_account" => "123456789"
    ]);

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'new_balance']));
    // the new balance of wallet should hold 100 after withdraw
    expect($response->json('new_balance'))->toBe(100);

});

it('assure user can generate transaction pdf', function () {
    // create user and make transaction (add funds)
    $user = User::factory()->has(Wallet::factory(['amount'=>0]))->create();
    // make sure the user has 0 balance 
    expect($user->wallet->amount)->toBe(0.0);
    $response = $this->actingAs($user)->post('/api/v1/wallet/add-funds',[
        "amount" => 500,
        "payment_method" => "bank_transfer"
    ]);

    // get the transaction PDF URL
    $response = $this->actingAs($user)->get('/api/v1/wallet/transactions/pdf');

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'pdf_url']));
    // the new balance of wallet should hold 100 after withdraw
    expect($response->json('pdf_url'))->toBeUrl();;

});

it('assure user can generate transfer QR Code', function () {
    // create user 
    $sender = User::factory()->has(Wallet::factory(['amount'=>100]))->create();
    $recipient = User::factory()->has(Wallet::factory(['amount'=>50]))->create();
    
    // get the transaction PDF URL
    $response = $this->actingAs($sender)->post('/api/v1/wallet/transfer/qr',[
        "amount" => 500,
        "recipient_id" => $recipient->id
    ]);

    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'qr_code_url']));
    // the new balance of wallet should hold 100 after withdraw
    expect($response->json('qr_code_url'))->toBeUrl();;

});


