<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('invoice_number'),
                TextEntry::make('company_name'),
                TextEntry::make('company_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('company_email')
                    ->placeholder('-'),
                TextEntry::make('company_phone')
                    ->placeholder('-'),
                TextEntry::make('company_logo')
                    ->placeholder('-'),
                TextEntry::make('client_name'),
                TextEntry::make('client_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('client_email')
                    ->placeholder('-'),
                TextEntry::make('client_phone')
                    ->placeholder('-'),
                TextEntry::make('invoice_date')
                    ->date(),
                TextEntry::make('due_date')
                    ->date(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('terms')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('tax_rate')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('template.name')
                    ->label('Template')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pdf_path')
                    ->placeholder('-'),
            ]);
    }
}
