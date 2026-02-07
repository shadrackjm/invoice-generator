@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#1e3a8a';
@endphp

<div class="classic-business bg-white p-12" style="min-height: 297mm;">
    
    {{-- Header with Border --}}
    <div class="border-b-4 pb-6 mb-8" style="border-color: {{ $primaryColor }}">
        <div class="flex justify-between items-center">
            <div>
                @if($invoice->company_logo)
                    <img src="{{ Storage::url($invoice->company_logo) }}" 
                         alt="{{ $invoice->company_name }}" 
                         class="h-20 mb-3">
                @endif
                <h1 class="text-2xl font-bold" style="color: {{ $primaryColor }}">
                    {{ $invoice->company_name }}
                </h1>
            </div>
            <div class="text-right">
                <div class="text-5xl font-bold mb-2" style="color: {{ $primaryColor }}">
                    INVOICE
                </div>
                <div class="text-xl font-semibold">
                    {{ $invoice->invoice_number }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- Two Column Layout --}}
    <div class="grid grid-cols-2 gap-8 mb-8">
        {{-- Left Column: Company & Bill To --}}
        <div class="space-y-6">
            {{-- From --}}
            <div>
                <div class="text-xs font-bold uppercase tracking-wider mb-2" 
                     style="color: {{ $primaryColor }}">
                    From
                </div>
                <div class="text-sm text-gray-800 space-y-1">
                    @if($invoice->company_address)
                        <p>{{ $invoice->company_address }}</p>
                    @endif
                    @if($invoice->company_email)
                        <p>{{ $invoice->company_email }}</p>
                    @endif
                    @if($invoice->company_phone)
                        <p>{{ $invoice->company_phone }}</p>
                    @endif
                </div>
            </div>
            
            {{-- Bill To --}}
            <div>
                <div class="text-xs font-bold uppercase tracking-wider mb-2" 
                     style="color: {{ $primaryColor }}">
                    Bill To
                </div>
                <div class="text-sm text-gray-800 space-y-1">
                    <p class="font-bold">{{ $invoice->client_name }}</p>
                    @if($invoice->client_address)
                        <p>{{ $invoice->client_address }}</p>
                    @endif
                    @if($invoice->client_email)
                        <p>{{ $invoice->client_email }}</p>
                    @endif
                    @if($invoice->client_phone)
                        <p>{{ $invoice->client_phone }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Right Column: Invoice Details --}}
        <div>
            <table class="w-full text-sm">
                <tr class="border-b border-gray-300">
                    <td class="py-2 font-semibold" style="color: {{ $primaryColor }}">
                        Invoice Number:
                    </td>
                    <td class="py-2 text-right">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                    <td class="py-2 font-semibold" style="color: {{ $primaryColor }}">
                        Invoice Date:
                    </td>
                    <td class="py-2 text-right">{{ $invoice->invoice_date->format('F d, Y') }}</td>
                </tr>
                <tr class="border-b border-gray-300">
                    <td class="py-2 font-semibold" style="color: {{ $primaryColor }}">
                        Due Date:
                    </td>
                    <td class="py-2 text-right">{{ $invoice->due_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td class="py-2 font-semibold" style="color: {{ $primaryColor }}">
                        Status:
                    </td>
                    <td class="py-2 text-right">
                        <span class="px-2 py-1 text-xs font-semibold rounded" 
                              style="background-color: {{ $primaryColor }}20; color: {{ $primaryColor }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    {{-- Line Items Table --}}
    <div class="mb-8">
        <table class="w-full">
            <thead>
                <tr class="text-white" style="background-color: {{ $primaryColor }}">
                    <th class="text-left py-3 px-4 font-semibold text-sm uppercase">
                        Description
                    </th>
                    <th class="text-center py-3 px-4 font-semibold text-sm uppercase w-20">
                        Qty
                    </th>
                    <th class="text-right py-3 px-4 font-semibold text-sm uppercase w-28">
                        Rate
                    </th>
                    <th class="text-right py-3 px-4 font-semibold text-sm uppercase w-32">
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="py-3 px-4 text-gray-800">{{ $item->description }}</td>
                        <td class="py-3 px-4 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-3 px-4 text-right text-gray-600">
                            ${{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-right font-semibold text-gray-800">
                            ${{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    {{-- Totals Section --}}
    <div class="flex justify-end mb-8">
        <div class="w-96">
            <table class="w-full text-sm">
                <tr class="border-b border-gray-300">
                    <td class="py-2 text-gray-700">Subtotal:</td>
                    <td class="py-2 text-right font-semibold">
                        ${{ number_format($invoice->subtotal, 2) }}
                    </td>
                </tr>
                <tr class="border-b border-gray-300">
                    <td class="py-2 text-gray-700">Tax ({{ $invoice->tax_rate }}%):</td>
                    <td class="py-2 text-right font-semibold">
                        ${{ number_format($invoice->tax_amount, 2) }}
                    </td>
                </tr>
                <tr class="text-white" style="background-color: {{ $primaryColor }}">
                    <td class="py-3 px-4 text-lg font-bold">TOTAL:</td>
                    <td class="py-3 px-4 text-right text-xl font-bold">
                        ${{ number_format($invoice->total, 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    {{-- Notes and Terms in Boxes --}}
    <div class="grid grid-cols-2 gap-6">
        @if($invoice->notes)
            <div class="border-2 p-4" style="border-color: {{ $primaryColor }}">
                <h4 class="text-sm font-bold uppercase tracking-wide mb-2" 
                    style="color: {{ $primaryColor }}">
                    Notes
                </h4>
                <p class="text-gray-700 text-sm">{{ $invoice->notes }}</p>
            </div>
        @endif
        
        @if($invoice->terms)
            <div class="border-2 p-4" style="border-color: {{ $primaryColor }}">
                <h4 class="text-sm font-bold uppercase tracking-wide mb-2" 
                    style="color: {{ $primaryColor }}">
                    Payment Terms
                </h4>
                <p class="text-gray-700 text-sm">{{ $invoice->terms }}</p>
            </div>
        @endif
    </div>
    
</div>