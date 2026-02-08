<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
        <h1 style="color: #2563eb; margin: 0 0 10px 0;">Invoice from {{ $invoice->company_name }}</h1>
        <p style="color: #6c757d; margin: 0;">Invoice Number: <strong>{{ $invoice->invoice_number }}</strong></p>
    </div>
    
    <div style="margin-bottom: 30px;">
        <p>Hello {{ $invoice->client_name }},</p>
        
        <p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> for the services provided.</p>
        
        <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0;"><strong>Invoice Date:</strong></td>
                    <td style="padding: 5px 0; text-align: right;">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0;"><strong>Due Date:</strong></td>
                    <td style="padding: 5px 0; text-align: right;">{{ $invoice->due_date->format('M d, Y') }}</td>
                </tr>
                <tr style="border-top: 2px solid #2563eb;">
                    <td style="padding: 10px 0;"><strong>Total Amount:</strong></td>
                    <td style="padding: 10px 0; text-align: right; font-size: 24px; color: #2563eb;">
                        <strong>${{ number_format($invoice->total, 2) }}</strong>
                    </td>
                </tr>
            </table>
        </div>
        
        @if($invoice->notes)
            <p><strong>Notes:</strong><br>{{ $invoice->notes }}</p>
        @endif
        
        @if($invoice->terms)
            <p><strong>Payment Terms:</strong><br>{{ $invoice->terms }}</p>
        @endif
    </div>
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #dee2e6;">
        <p style="color: #6c757d; font-size: 14px; margin: 0;">
            Thank you for your business!
        </p>
        <p style="color: #6c757d; font-size: 14px; margin: 10px 0 0 0;">
            {{ $invoice->company_name }}<br>
            @if($invoice->company_email)
                {{ $invoice->company_email }}<br>
            @endif
            @if($invoice->company_phone)
                {{ $invoice->company_phone }}
            @endif
        </p>
    </div>
    
</body>
</html>