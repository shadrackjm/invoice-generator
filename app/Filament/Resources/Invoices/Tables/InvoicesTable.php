<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\Invoice;
use App\Mail\InvoiceMail;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Mail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('client_name')
                    ->searchable(),
                TextColumn::make('invoice_date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('total')
                    ->money('usd')
                    ->sortable(),
                TextColumn::make('template.name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'sent',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pdf_path')
                    ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Filter::make('overdue')
                    ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now())->where('status', '!=', 'paid'))
                    ->label('Overdue')
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Invoice $record): string => route('invoice.download', $record))
                    ->openUrlInNewTab(),
                Action::make('resend')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(fn(Invoice $record) => static::resendInvoice($record))
                    ->visible(fn(Invoice $record): bool => !empty($record->client_email)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function resendInvoice(Invoice $invoice): void
    {
        if (!$invoice->client_email) {
            Notification::make()
                ->title('Cannot send email')
                ->body('Client email is required to send invoice.')
                ->danger()
                ->send();
            return;
        }

        Mail::to($invoice->client_email)->queue(new InvoiceMail($invoice));

        $invoice->update(['status' => 'sent']);

        Notification::make()
            ->title('Invoice sent')
            ->body('Invoice has been sent to ' . $invoice->client_email)
            ->success()
            ->send();
    }
}
