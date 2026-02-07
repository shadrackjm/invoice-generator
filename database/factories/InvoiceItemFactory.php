<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    private static array $descriptions = [
        'Web Development Services',
        'UI/UX Design',
        'Logo Design',
        'SEO Optimization',
        'Content Writing',
        'Social Media Management',
        'Server Maintenance',
        'Database Administration',
        'API Integration',
        'Mobile App Development',
        'Consulting Hours',
        'Project Management',
        'Quality Assurance Testing',
        'Cloud Hosting (Monthly)',
        'Domain Registration',
        'SSL Certificate',
        'Email Marketing Campaign',
        'Photography Services',
        'Video Editing',
        'Technical Support',
    ];

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 25, 500);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->randomElement(self::$descriptions),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $quantity * $unitPrice,
            'sort_order' => 0,
        ];
    }
}
