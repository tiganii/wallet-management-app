<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'amount' => fake()->randomFloat(2),
            'new_balance' => fake()->randomFloat(2),
            'transaction_type' => fake()->randomElement(TransactionType::values()),
            'status' => fake()->randomElement(TransactionStatus::values()),
            'payment_method' => fake()->word,
            'notes' => fake()->sentence
        ];
    }
}
