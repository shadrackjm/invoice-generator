<?php

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoice/preview/{invoice}', function(Invoice $invoice){
    return view('invoice-preview', compact('invoice'));
})->name('invoice.preview');

Route::post('/invoice/{invoice}/change-template', function (Invoice $invoice, Request $request) {
    $invoice->update(['template_id' => $request->template_id]);
    return redirect()->route('invoice.preview', $invoice);
})->name('invoice.change-template');

