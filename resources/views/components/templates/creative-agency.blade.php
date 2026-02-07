@props(['invoice', 'forPdf' => false])

@php
    $primaryColor = $invoice->template->settings['primary_color'] ?? '#7c3aed';
@endphp

<div class="creative-agency bg-white" style="min-height: 297mm;">
    
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