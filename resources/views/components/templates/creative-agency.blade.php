@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#7c3aed';
@endphp

@if($forPdf)
{{-- PDF Version: uses tables and inline styles for DomPDF compatibility --}}
<div style="font-family: 'DejaVu Sans', sans-serif; font-size: 14px; color: #1f2937;">

    {{-- Colored Header Banner --}}
    <div style="background-color: {{ $primaryColor }}; padding: 40px 40px 30px 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top; color: #ffffff;">
                    @if($invoice->company_logo)
                        <img src="{{ storage_path('app/public/' . $invoice->company_logo) }}"
                             alt="{{ $invoice->company_name }}"
                             style="height: 50px; margin-bottom: 12px;">
                    @endif
                    <div style="font-size: 30px; font-weight: bold; margin-bottom: 8px; color: #ffffff;">
                        {{ $invoice->company_name }}
                    </div>
                    <div style="color: rgba(255,255,255,0.9); font-size: 12px;">
                        @if($invoice->company_email)
                            <p style="margin-bottom: 2px;">{{ $invoice->company_email }}</p>
                        @endif
                        @if($invoice->company_phone)
                            <p style="margin-bottom: 2px;">{{ $invoice->company_phone }}</p>
                        @endif
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top; color: #ffffff;">
                    <div style="font-size: 44px; font-weight: 900; margin-bottom: 8px; color: #ffffff;">INVOICE</div>
                    <div style="font-size: 20px; font-weight: bold; color: #ffffff;">{{ $invoice->invoice_number }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Main Content --}}
    <div style="padding: 40px;">

        {{-- Dates and Client Info --}}
        <table style="width: 100%; margin-bottom: 35px;">
            <tr>
                <td style="width: 33%; vertical-align: top;">
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
                        Invoice Date
                    </div>
                    <div style="font-size: 16px; font-weight: 600;">
                        {{ $invoice->invoice_date->format('M d, Y') }}
                    </div>
                </td>
                <td style="width: 33%; vertical-align: top;">
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
                        Due Date
                    </div>
                    <div style="font-size: 16px; font-weight: 600;">
                        {{ $invoice->due_date->format('M d, Y') }}
                    </div>
                </td>
                <td style="width: 34%; vertical-align: top;">
                    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
                        Billed To
                    </div>
                    <div style="font-size: 12px;">
                        <p style="font-weight: bold; font-size: 16px; margin-bottom: 4px;">{{ $invoice->client_name }}</p>
                        @if($invoice->client_email)
                            <p style="color: #4b5563;">{{ $invoice->client_email }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Services Label --}}
        <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; color: {{ $primaryColor }};">
            Services
        </div>

        {{-- Line Items as Card-style rows --}}
        @foreach($invoice->items as $item)
            <table style="width: 100%; margin-bottom: 8px; background-color: {{ $primaryColor }}08; border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 15px; vertical-align: middle;">
                        <div style="font-weight: 600; color: #1f2937;">{{ $item->description }}</div>
                        <div style="font-size: 12px; color: #4b5563; margin-top: 4px;">
                            {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                        </div>
                    </td>
                    <td style="padding: 12px 15px; text-align: right; vertical-align: middle; font-size: 18px; font-weight: bold; color: {{ $primaryColor }};">
                        ${{ number_format($item->total, 2) }}
                    </td>
                </tr>
            </table>
        @endforeach

        {{-- Totals --}}
        <table style="width: 100%; margin-top: 25px;">
            <tr>
                <td style="width: 55%;"></td>
                <td style="width: 45%;">
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="padding: 6px 0; color: #374151;">Subtotal</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; color: #374151;">Tax ({{ $invoice->tax_rate }}%)</td>
                            <td style="padding: 6px 0; text-align: right; font-weight: 600;">${{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                    </table>

                    <table style="width: 100%; margin-top: 12px; background-color: {{ $primaryColor }}; color: #ffffff;">
                        <tr>
                            <td style="padding: 18px 20px; font-size: 18px; font-weight: bold;">TOTAL</td>
                            <td style="padding: 18px 20px; text-align: right; font-size: 24px; font-weight: 900;">${{ number_format($invoice->total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Notes and Terms --}}
        @if($invoice->notes || $invoice->terms)
            <div style="margin-top: 40px;">
                @if($invoice->notes)
                    <div style="background-color: {{ $primaryColor }}08; padding: 18px; margin-bottom: 15px;">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
                            Additional Notes
                        </div>
                        <p style="color: #374151; font-size: 12px;">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if($invoice->terms)
                    <div style="background-color: {{ $primaryColor }}08; padding: 18px;">
                        <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; color: {{ $primaryColor }};">
                            Payment Terms
                        </div>
                        <p style="color: #374151; font-size: 12px;">{{ $invoice->terms }}</p>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

@else
{{-- Browser Version: uses Tailwind CSS --}}
<div class="creative-agency bg-white" style="min-height: 297mm; padding: 48px;">

    {{-- Colored Header Banner --}}
    <div class="p-12 pb-8" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}dd 100%)">
        <div class="flex justify-between items-start text-white">
            <div>
                @if($invoice->company_logo)
                    <img src="{{ Storage::url($invoice->company_logo) }}"
                         alt="{{ $invoice->company_name }}"
                         class="h-16 mb-4 brightness-0 invert">
                @endif
                <h1 class="text-4xl font-bold mb-2">{{ $invoice->company_name }}</h1>
                <div class="text-white/90 space-y-1">
                    @if($invoice->company_email)
                        <p>{{ $invoice->company_email }}</p>
                    @endif
                    @if($invoice->company_phone)
                        <p>{{ $invoice->company_phone }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-6xl font-black mb-2">INVOICE</div>
                <div class="text-2xl font-bold">{{ $invoice->invoice_number }}</div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="p-12">

        {{-- Dates and Client Info Side by Side --}}
        <div class="grid grid-cols-3 gap-8 mb-10">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3"
                     style="color: {{ $primaryColor }}">
                    Invoice Date
                </div>
                <div class="text-lg font-semibold">
                    {{ $invoice->invoice_date->format('M d, Y') }}
                </div>
            </div>

            <div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3"
                     style="color: {{ $primaryColor }}">
                    Due Date
                </div>
                <div class="text-lg font-semibold">
                    {{ $invoice->due_date->format('M d, Y') }}
                </div>
            </div>

            <div>
                <div class="text-xs font-bold uppercase tracking-wider mb-3"
                     style="color: {{ $primaryColor }}">
                    Billed To
                </div>
                <div class="text-sm space-y-1">
                    <p class="font-bold text-lg">{{ $invoice->client_name }}</p>
                    @if($invoice->client_email)
                        <p class="text-gray-600">{{ $invoice->client_email }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Line Items with Modern Card Style --}}
        <div class="mb-10 space-y-3">
            <div class="text-xs font-bold uppercase tracking-wider mb-4"
                 style="color: {{ $primaryColor }}">
                Services
            </div>

            @foreach($invoice->items as $item)
                <div class="flex justify-between items-center p-4 rounded-lg"
                     style="background-color: {{ $primaryColor }}08">
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800">{{ $item->description }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $item->quantity }} × ${{ number_format($item->unit_price, 2) }}
                        </div>
                    </div>
                    <div class="text-xl font-bold" style="color: {{ $primaryColor }}">
                        ${{ number_format($item->total, 2) }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals with Accent --}}
        <div class="flex justify-end">
            <div class="w-96">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-700">
                        <span>Subtotal</span>
                        <span class="font-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Tax ({{ $invoice->tax_rate }}%)</span>
                        <span class="font-semibold">${{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-6 rounded-lg text-white"
                     style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}dd 100%)">
                    <span class="text-xl font-bold">TOTAL</span>
                    <span class="text-3xl font-black">${{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes and Terms --}}
        @if($invoice->notes || $invoice->terms)
            <div class="mt-12 space-y-6">
                @if($invoice->notes)
                    <div class="p-6 rounded-lg" style="background-color: {{ $primaryColor }}08">
                        <h4 class="text-sm font-bold uppercase tracking-wider mb-3"
                            style="color: {{ $primaryColor }}">
                            Additional Notes
                        </h4>
                        <p class="text-gray-700">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if($invoice->terms)
                    <div class="p-6 rounded-lg" style="background-color: {{ $primaryColor }}08">
                        <h4 class="text-sm font-bold uppercase tracking-wider mb-3"
                            style="color: {{ $primaryColor }}">
                            Payment Terms
                        </h4>
                        <p class="text-gray-700">{{ $invoice->terms }}</p>
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
@endif