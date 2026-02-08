@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#1e3a8a';
@endphp

@if($forPdf)
{{-- PDF Version: uses tables and inline styles for DomPDF compatibility --}}
<div style="font-family: 'DejaVu Sans', sans-serif; font-size: 14px; color: #1f2937; padding: 40px;">

    {{-- Header with Border --}}
    <table style="width: 100%; margin-bottom: 30px; border-bottom: 4px solid {{ $primaryColor }}; padding-bottom: 20px;">
        <tr>
            <td style="vertical-align: middle;">
                @if($invoice->company_logo)
                    <img src="{{ storage_path('app/public/' . $invoice->company_logo) }}"
                         alt="{{ $invoice->company_name }}"
                         style="height: 60px; margin-bottom: 10px;">
                @endif
                <div style="font-size: 24px; font-weight: bold; color: {{ $primaryColor }};">
                    {{ $invoice->company_name }}
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div style="font-size: 40px; font-weight: bold; color: {{ $primaryColor }}; margin-bottom: 8px;">
                    INVOICE
                </div>
                <div style="font-size: 18px; font-weight: 600;">
                    {{ $invoice->invoice_number }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Two Column Layout --}}
    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            {{-- Left Column: Company & Bill To --}}
            <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                {{-- From --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                        From
                    </div>
                    <div style="font-size: 12px; color: #1f2937;">
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
                </div>

                {{-- Bill To --}}
                <div>
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                        Bill To
                    </div>
                    <div style="font-size: 12px; color: #1f2937;">
                        <p style="font-weight: bold; margin-bottom: 2px;">{{ $invoice->client_name }}</p>
                        @if($invoice->client_address)
                            <p style="margin-bottom: 2px;">{{ $invoice->client_address }}</p>
                        @endif
                        @if($invoice->client_email)
                            <p style="margin-bottom: 2px;">{{ $invoice->client_email }}</p>
                        @endif
                        @if($invoice->client_phone)
                            <p style="margin-bottom: 2px;">{{ $invoice->client_phone }}</p>
                        @endif
                    </div>
                </div>
            </td>

            {{-- Right Column: Invoice Details --}}
            <td style="width: 50%; vertical-align: top;">
                <table style="width: 100%; font-size: 12px;">
                    <tr style="border-bottom: 1px solid #d1d5db;">
                        <td style="padding: 8px 0; font-weight: 600; color: {{ $primaryColor }};">
                            Invoice Number:
                        </td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #d1d5db;">
                        <td style="padding: 8px 0; font-weight: 600; color: {{ $primaryColor }};">
                            Invoice Date:
                        </td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_date->format('F d, Y') }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #d1d5db;">
                        <td style="padding: 8px 0; font-weight: 600; color: {{ $primaryColor }};">
                            Due Date:
                        </td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->due_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: {{ $primaryColor }};">
                            Status:
                        </td>
                        <td style="padding: 8px 0; text-align: right;">
                            <span style="background-color: {{ $primaryColor }}20; color: {{ $primaryColor }}; padding: 2px 8px; font-size: 10px; font-weight: 600;">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Line Items Table --}}
    <table style="width: 100%; margin-bottom: 30px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: {{ $primaryColor }}; color: #ffffff;">
                <th style="text-align: left; padding: 10px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">
                    Description
                </th>
                <th style="text-align: center; padding: 10px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; width: 60px;">
                    Qty
                </th>
                <th style="text-align: right; padding: 10px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; width: 100px;">
                    Rate
                </th>
                <th style="text-align: right; padding: 10px 12px; font-weight: 600; font-size: 12px; text-transform: uppercase; width: 110px;">
                    Amount
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr style="background-color: {{ $loop->even ? '#f9fafb' : '#ffffff' }};">
                    <td style="padding: 10px 12px; color: #1f2937;">{{ $item->description }}</td>
                    <td style="padding: 10px 12px; text-align: center; color: #4b5563;">{{ $item->quantity }}</td>
                    <td style="padding: 10px 12px; text-align: right; color: #4b5563;">
                        ${{ number_format($item->unit_price, 2) }}
                    </td>
                    <td style="padding: 10px 12px; text-align: right; font-weight: 600; color: #1f2937;">
                        ${{ number_format($item->total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals Section --}}
    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%;">
                <table style="width: 100%; font-size: 12px;">
                    <tr style="border-bottom: 1px solid #d1d5db;">
                        <td style="padding: 8px 0; color: #374151;">Subtotal:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600;">
                            ${{ number_format($invoice->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #d1d5db;">
                        <td style="padding: 8px 0; color: #374151;">Tax ({{ $invoice->tax_rate }}%):</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: 600;">
                            ${{ number_format($invoice->tax_amount, 2) }}
                        </td>
                    </tr>
                    <tr style="background-color: {{ $primaryColor }}; color: #ffffff;">
                        <td style="padding: 10px 12px; font-size: 16px; font-weight: bold;">TOTAL:</td>
                        <td style="padding: 10px 12px; text-align: right; font-size: 18px; font-weight: bold;">
                            ${{ number_format($invoice->total, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Notes and Terms --}}
    <table style="width: 100%;">
        <tr>
            @if($invoice->notes)
                <td style="width: 48%; vertical-align: top; border: 2px solid {{ $primaryColor }}; padding: 12px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                        Notes
                    </div>
                    <p style="color: #374151; font-size: 12px;">{{ $invoice->notes }}</p>
                </td>
            @endif
            @if($invoice->notes && $invoice->terms)
                <td style="width: 4%;"></td>
            @endif
            @if($invoice->terms)
                <td style="width: 48%; vertical-align: top; border: 2px solid {{ $primaryColor }}; padding: 12px;">
                    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; color: {{ $primaryColor }};">
                        Payment Terms
                    </div>
                    <p style="color: #374151; font-size: 12px;">{{ $invoice->terms }}</p>
                </td>
            @endif
        </tr>
    </table>

</div>

@else
{{-- Browser Version: uses Tailwind CSS --}}
<div class="classic-business bg-white p-12" style="min-height: 297mm; padding: 48px;">

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
@endif