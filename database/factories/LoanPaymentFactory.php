<?php

namespace Database\Factories;

use App\Models\Calculation;
use App\Models\LoanPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanPayment>
 */
class LoanPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'calculation_id' => Calculation::factory(),
            'amount' => fake()->randomFloat(2, 500, 50_000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
