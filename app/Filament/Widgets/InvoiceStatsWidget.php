<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();
        return [
            Stat::make('Total Invoices', Invoice::where('user_id', $userId)->count())
                ->description('All time')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),
            Stat::make('Paid Invoices', Invoice::where('user_id', $userId)->where('status', 'paid')->count())
                ->description('Successfully paid')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total Revenue', '$' . number_format(Invoice::where('user_id', $userId)->where('status', 'paid')->sum('total'), 2))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Overdue', Invoice::where('user_id', $userId)
                ->where('due_date', '<', now())
                ->where('status', '!=', 'paid')
                ->count())
                ->description('Need attention')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
        ];
    }
}
