<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Preview - {{ $invoice->invoice_number }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    
    <div class="max-w-5xl mx-auto py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <x-invoice-renderer :invoice="$invoice" />
        </div>
        
        <div class="mt-4 flex justify-center space-x-4">
            @foreach(\App\Models\Template::active()->get() as $template)
                <form method="POST" action="{{ route('invoice.change-template', $invoice) }}">
                    @csrf
                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg border-2 {{ $invoice->template_id === $template->id ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }}">
                        {{ $template->name }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    
</body>
</html>