<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Template;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $invoiceDate = fake()->dateTimeBetween('-3 months', 'now');
        $dueDate = fake()->dateTimeBetween($invoiceDate, '+1 month');
        $taxRate = fake()->randomElement([0, 5, 10, 15, 16]);

        return [
            'user_id' => User::factory(),
            'invoice_number' => 'INV-' . now()->year . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'company_name' => fake()->company(),
            'company_email' => fake()->companyEmail(),
            'company_phone' => fake()->phoneNumber(),
            'company_address' => fake()->address(),
            'company_logo' => null,
            'client_name' => fake()->name(),
            'client_email' => fake()->safeEmail(),
            'client_phone' => fake()->phoneNumber(),
            'client_address' => fake()->address(),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'notes' => fake()->optional(0.5)->sentence(),
            'terms' => fake()->optional(0.7)->sentence(),
            'subtotal' => 0,
            'tax_rate' => $taxRate,
            'tax_amount' => 0,
            'total' => 0,
            'status' => fake()->randomElement(['draft', 'sent', 'paid']),
            'template_id' => Template::inRandomOrder()->first()?->id,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }
}
