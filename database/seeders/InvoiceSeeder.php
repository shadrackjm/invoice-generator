<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Invoice::factory()
            ->count(5)
            ->for($user)
            ->create()
            ->each(function (Invoice $invoice) {
                $itemCount = rand(2, 5);

                InvoiceItem::factory()
                    ->count($itemCount)
                    ->for($invoice)
                    ->create()
                    ->each(function ($item, $index) {
                        $item->update(['sort_order' => $index + 1]);
                    });
            });
    }
}