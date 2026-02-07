<div class="invoice-container bg-white" 
    style="font-family: {{ $invoice->template->settings['font-family'] ?? 'Arial' }}">

    {{-- This is the base container - specific template will override this --}}
    <div class="invoice-content">
        {{ $slot }}
    </div>
</div>

<style>
    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 100%;
            max-width: none;
        }

        .invoice-container {
            @apply max-w-4xl mz-auto p-0;
        }
    }
</style>