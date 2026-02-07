@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#2563eb';
@endphp

<div class="modern-minimalist bg-white p-12" style="min-height: 297mm;">

    {{-- Header Section --}}
    <div class="flex justify-between items-start mb-12">
        {{-- Company Info --}}
        <div class="flex-1">
            @if($invoice->company_logo)
                <img src="{{ Storage::url($invoice->company_logo) }}" 
                     alt="{{ $invoice->company_name }}" 
                     class="h-16 mb-4">
            @endif
            <h1 class="text-3xl font-bold mb-2" style="color: {{ $primaryColor }}">
                {{ $invoice->company_name }}
            </h1>
            <div class="text-gray-600 text-sm space-y-1">
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
        {{-- Invoice Details --}}
        <div class="text-right">
            <h2 class="text-4xl font-bold mb-6" style="color: {{ $primaryColor }}">
                INVOICE
            </h2>
            <div class="text-sm space-y-2">
                <div>
                    <span class="text-gray-600">Invoice Number:</span>
                    <strong class="ml-2">{{ $invoice->invoice_number }}</strong>
                </div>
                <div>
                    <span class="text-gray-600">Invoice Date:</span>
                    <strong class="ml-2">{{ $invoice->invoice_date->format('M d, Y') }}</strong>
                </div>
                <div>
                    <span class="text-gray-600">Due Date:</span>
                    <strong class="ml-2">{{ $invoice->due_date->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Bill To Section --}}
    <div class="mb-12">
        <h3 class="text-sm font-bold uppercase tracking-wide mb-3" 
            style="color: {{ $primaryColor }}">
            Bill To
        </h3>
        <div class="text-gray-800">
            <p class="font-semibold text-lg mb-1">{{ $invoice->client_name }}</p>
            @if($invoice->client_address)
                <p class="text-sm">{{ $invoice->client_address }}</p>
            @endif
            @if($invoice->client_email)
                <p class="text-sm">{{ $invoice->client_email }}</p>
            @endif
            @if($invoice->client_phone)
                <p class="text-sm">{{ $invoice->client_phone }}</p>
            @endif
        </div>
    </div>

    {{-- Line Items Table --}}
    <div class="mb-12">
        <table class="w-full">
            <thead>
                <tr class="border-b-2" style="border-color: {{ $primaryColor }}">
                    <th class="text-left py-3 font-semibold text-sm uppercase tracking-wide" 
                        style="color: {{ $primaryColor }}">
                        Description
                    </th>
                    <th class="text-center py-3 font-semibold text-sm uppercase tracking-wide w-24" 
                        style="color: {{ $primaryColor }}">
                        Qty
                    </th>
                    <th class="text-right py-3 font-semibold text-sm uppercase tracking-wide w-32" 
                        style="color: {{ $primaryColor }}">
                        Unit Price
                    </th>
                    <th class="text-right py-3 font-semibold text-sm uppercase tracking-wide w-32" 
                        style="color: {{ $primaryColor }}">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr class="border-b border-gray-200">
                        <td class="py-4 text-gray-800">{{ $item->description }}</td>
                        <td class="py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-4 text-right text-gray-600">
                            ${{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="py-4 text-right font-semibold text-gray-800">
                            ${{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals Section --}}
    <div class="flex justify-end mb-12">
        <div class="w-80">
            <div class="flex justify-between py-2 text-gray-700">
                <span>Subtotal:</span>
                <span class="font-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 text-gray-700">
                <span>Tax ({{ $invoice->tax_rate }}%):</span>
                <span class="font-semibold">${{ number_format($invoice->tax_amount, 2) }}</span>
            </div>
            <div class="flex justify-between py-4 border-t-2 text-lg font-bold" 
                 style="color: {{ $primaryColor }}; border-color: {{ $primaryColor }}">
                <span>Total:</span>
                <span>${{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Notes and Terms --}}
    <div class="space-y-6">
        @if($invoice->notes)
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide mb-2" 
                    style="color: {{ $primaryColor }}">
                    Notes
                </h4>
                <p class="text-gray-700 text-sm">{{ $invoice->notes }}</p>
            </div>
        @endif
        
        @if($invoice->terms)
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wide mb-2" 
                    style="color: {{ $primaryColor }}">
                    Payment Terms
                </h4>
                <p class="text-gray-700 text-sm">{{ $invoice->terms }}</p>
            </div>
        @endif
    </div>
</div>