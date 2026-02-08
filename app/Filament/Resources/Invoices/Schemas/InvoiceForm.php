<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Details')
                    ->columnSpanFull()
                    ->columns(2)
                    ->components([
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->required(),
                        Select::make('template_id')
                            ->relationship('template', 'name')
                            ->required(),
                        DatePicker::make('invoice_date')
                            ->label('Invoice Date')
                            ->required(),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                            ])
                            ->required(),
                        Textarea::make('notes')
                            ->label('Notes'),
                    ]),
                Section::make('Company Information')
                    ->columns(2)
                    ->components([
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required(),
                        TextInput::make('company_address')
                            ->label('Company Address')
                            ->required(),
                        TextInput::make('company_email')
                            ->label('Company Email')
                            ->email()
                            ->required(),
                        TextInput::make('company_phone')
                            ->label('Company Phone')
                            ->tel()
                            ->required(),
                    ]),
                Section::make('Client Information')
                    ->columns(2)
                    ->components([
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required(),
                        TextInput::make('client_address')
                            ->label('Client Address')
                            ->required(),
                        TextInput::make('client_email')
                            ->label('Client Email')
                            ->email()
                            ->required(),
                        TextInput::make('client_phone')
                            ->label('Client Phone')
                            ->tel()
                            ->required(),
                    ]),
                Section::make('Financial Details')
                    ->columns(2)
                    ->components([
                        TextInput::make('subtotal')
                                ->numeric()
                                ->prefix('$')
                                ->disabled()
                                ->dehydrated(),
                        TextInput::make('tax_rate')
                            ->label('Tax (%)')
                            ->numeric()
                            ->required(),
                        TextInput::make('total')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                    ]),
                Section::make('Additional Information')
                    ->columns(1)
                    ->components([
                        Textarea::make('notes')
                            ->rows(3),
                        Textarea::make('terms')
                            ->rows(3),
                    ]),
            ]);
    }
}
