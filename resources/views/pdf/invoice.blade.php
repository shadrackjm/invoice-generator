<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #1f2937;
        }

        table {
            border-collapse: collapse;
        }

        p {
            margin: 0;
            padding: 0;
        }

        img {
            display: block;
        }
    </style>
</head>
<body>
    <x-invoice-renderer :invoice="$invoice" :forPdf="true" />
</body>
</html>