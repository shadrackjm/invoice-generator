<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Mail\InvoiceMail;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Invoices\InvoiceResource;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('invoice.download', $this->record))
                ->openUrlInNewTab(),
            Action::make('resend')
                ->label('Send via Email')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    if (!$this->record->client_email) {
                        Notification::make()
                            ->title('Cannot send email')
                            ->body('Client email is required.')
                            ->danger()
                            ->send();
                        return;
                    }
                    
                    Mail::to($this->record->client_email)->queue(new InvoiceMail($this->record));
                    $this->record->update(['status' => 'sent']);
                    
                    Notification::make()
                        ->title('Invoice sent')
                        ->body('Sent to ' . $this->record->client_email)
                        ->success()
                        ->send();
                })
                ->visible(fn () => !empty($this->record->client_email)),
            EditAction::make(),
            DeleteAction::make()
        ];
    }
}
