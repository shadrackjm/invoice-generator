<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf;
    }
    
    public function download(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = $this->generate($invoice);
        
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function stream(Invoice $invoice): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generate($invoice);
        
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        
        return $pdf->stream($filename);
    }
    
    public function save(Invoice $invoice, string $path): string
    {
        $pdf = $this->generate($invoice);
        
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';
        $fullPath = $path . '/' . $filename;
        
        $pdf->save($fullPath);
        
        return $fullPath;
    }
}