@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#2563eb';
@endphp

@if($forPdf)
{{-- PDF Version: uses tables and inline styles for DomPDF compatibility --}}
<div style="font-family: 'DejaVu Sans', sans-serif; font-size: 14px; color: #1f2937; padding: 40px;">

    {{-- Header Section --}}
    <table style="width: 100%; margin-bottom: 40px;">
        <tr>
            {{-- Company Info --}}
            <td style="vertical-align: top;">
                @if($invoice->company_logo)
                    <img src="{{ storage_path('app/public/' . $invoice->company_logo) }}"
                         alt="{{ $invoice->company_name }}"
                         style="height: 50px; margin-bottom: 12px;">
                @endif
                <div style="font-size: 26px; font-weight: bold; margin-bottom: 8px; color: {{ $primaryColor }};">
                    {{ $invoice->company_name }}
                </div>
                <div style="color: #4b5563; font-size: 12px;">
                    @if($invoice->company_address)
                        <p style="margin-bottom: 2px;">{{ $invoice->company_address }}</p>
                    @endif
                    @if($invoice->company_email)
                        <p style="margin-bottom: 2px;">{{ $invoice->company_email }}</p>
                    @endif
                    @if($invoice->company_phone)
                        <p style="margin-bottom: 2px;">{{ $invoice->company_phone }}</p>
                    @endif
                </div>
            </td>
            {{-- Invoice Details --}}
            <td style="text-align: right; vertical-align: top;">
                <div style="font-size: 32px; font-weight: bold; margin-bottom: 20px; color: {{ $primaryColor }};">
                    INVOICE
                </div>
                <div style="font-size: 12px;">
                    <div style="margin-bottom: 6px;">
                        <span style="color: #4b5563;">Invoice Number:</span>
                        <strong style="margin-left: 8px;">{{ $invoice->invoice_number }}</strong>
                    </div>
                    <div style="margin-bottom: 6px;">
                        <span style="color: #4b5563;">Invoice Date:</span>
                        <strong style="margin-left: 8px;">{{ $invoice->invoice_date->format('M d, Y') }}</strong>
                    </div>
                    <div>
                        <span style="color: #4b5563;">Due Date:</span>
                        <strong style="margin-left: 8px;">{{ $invoice->due_date->format('M d, Y') }}</strong>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Bill To Section --}}
    <div style="margin-bottom: 40px;">
        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
            Bill To
        </div>
        <div style="color: #1f2937;">
            <p style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">{{ $invoice->client_name }}</p>
            @if($invoice->client_address)
                <p style="font-size: 12px; margin-bottom: 2px;">{{ $invoice->client_address }}</p>
            @endif
            @if($invoice->client_email)
                <p style="font-size: 12px; margin-bottom: 2px;">{{ $invoice->client_email }}</p>
            @endif
            @if($invoice->client_phone)
                <p style="font-size: 12px; margin-bottom: 2px;">{{ $invoice->client_phone }}</p>
            @endif
        </div>
    </div>

    {{-- Line Items Table --}}
    <table style="width: 100%; margin-bottom: 40px; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid {{ $primaryColor }};">
                <th style="text-align: left; padding: 10px 0; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: {{ $primaryColor }};">
                    Description
                </th>
                <th style="text-align: center; padding: 10px 0; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: {{ $primaryColor }}; width: 80px;">
                    Qty
                </th>
                <th style="text-align: right; padding: 10px 0; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: {{ $primaryColor }}; width: 110px;">
                    Unit Price
                </th>
                <th style="text-align: right; padding: 10px 0; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: {{ $primaryColor }}; width: 110px;">
                    Total
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 0; color: #1f2937;">{{ $item->description }}</td>
                    <td style="padding: 12px 0; text-align: center; color: #4b5563;">{{ $item->quantity }}</td>
                    <td style="padding: 12px 0; text-align: right; color: #4b5563;">
                        ${{ number_format($item->unit_price, 2) }}
                    </td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 600; color: #1f2937;">
                        ${{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals Section --}}
    <table style="width: 100%; margin-bottom: 40px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <table style="width: 100%; font-size: 13px;">
                    <tr>
                        <td style="padding: 6px 0; color: #374151;">Subtotal:</td>
                        <td style="padding: 6px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #374151;">Tax ({{ $invoice->tax_rate }}%):</td>
                        <td style="padding: 6px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                    <tr style="border-top: 2px solid {{ $primaryColor }};">
                        <td style="padding: 12px 0; font-size: 16px; font-weight: bold; color: {{ $primaryColor }};">Total:</td>
                        <td style="padding: 12px 0; text-align: right; font-size: 16px; font-weight: bold; color: {{ $primaryColor }};">${{ number_format($invoice->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Notes and Terms --}}
    @if($invoice->notes)
        <div style="margin-bottom: 20px;">
            <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                Notes
            </div>
            <p style="color: #374151; font-size: 12px;">{{ $invoice->notes }}</p>
        </div>
    @endif

    @if($invoice->terms)
        <div>
            <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                Payment Terms
            </div>
            <p style="color: #374151; font-size: 12px;">{{ $invoice->terms }}</p>
        </div>
    @endif

</div>

@else
{{-- Browser Version: uses Tailwind CSS --}}
<div class="modern-minimalist" style="min-height: 297mm; padding: 48px;">

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
@endif