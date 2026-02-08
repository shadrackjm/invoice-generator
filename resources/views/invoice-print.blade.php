<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice {{ $invoice->invoice_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-container {
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>
<body class="bg-white">
    
    <div class="no-print fixed top-4 right-4 space-x-2 z-50">
        <button 
            onclick="window.print()" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold"
        >
            Print
        </button>
        <button 
            onclick="window.close()" 
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold"
        >
            Close
        </button>
    </div>
    
    <div class="print-container">
        <x-invoice-renderer :invoice="$invoice" :forPdf="false" />
    </div>
    
    <script>
        // Auto-trigger print dialog after page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
    
</body>
</html>