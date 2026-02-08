<?php

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Route;

// we'll add our public invoice routes here

Route::livewire('create-invoice', 'pages::invoice.create')
->name('create-invoice');

Route::get('/invoice/{invoice}/download', function (Invoice $invoice) {
    // Check authorization
    if ($invoice->user_id !== Auth::id()) {
        abort(403);
    }
    
    $pdfService = new InvoicePdfService();
    return $pdfService->download($invoice);
})->middleware('auth')->name('invoice.download');