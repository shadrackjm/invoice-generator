<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Template;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoicePdfService;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;

new #[Layout('layouts.public')] class extends Component implements HasActions, HasSchemas {
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];
    public ?int $selectedTemplateId = 1;

    public function mount(): void
    {
        $savedData = session()->pull('invoice_data');

        if ($savedData) {
            $this->form->fill($savedData);
        } else {
            $this->form->fill([
                'template_id' => 1,
                'invoice_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'tax_rate' => 10,
                'items' => [
                    [
                        'description' => '',
                        'quantity' => 1,
                        'unit_price' => 0,
                    ]
                ],
            ]);
        }

        // handle pending action after auth redirect
        if (Auth::check()) {
            $pendingAction = session()->pull('pending_action');

            if ($pendingAction && $this->validateInvoiceData()) {
                if ($pendingAction === 'download') {
                    $this->handleDownload();
                } elseif ($pendingAction === 'email') {
                    $this->handleEmail();
                }
            }
        }
    }

    // the form
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        // Company information
                        Section::make('Company Information')
                            ->columnSpanFull()
                            ->description('Your business details')
                            ->schema([
                                TextInput::make('company_name')
                                    ->label('Company Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Your Company Name')
                                    ->live(debounce: 500),

                                Textarea::make('company_address')
                                    ->label('Address')
                                    ->rows(3)
                                    ->placeholder('123 Business Street, City, Country')
                                    ->live(debounce: 500),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('company_email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('hello@company.com')
                                            ->live(onBlur: true),

                                        TextInput::make('company_phone')
                                            ->label('Phone')
                                            ->tel()
                                            ->placeholder('+1 (555) 123-4567')
                                            ->live(onBlur: true),
                                    ]),
                            ])
                    ])->columnSpan(1),

                // Client Information
                Section::make('Client Information')
                    ->description('Bill to')
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Client Company Name')
                            ->live(debounce: 500),

                        Textarea::make('client_address')
                            ->label('Address')
                            ->rows(3)
                            ->placeholder('456 Client Avenue, City, Country')
                            ->live(debounce: 500),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('client_email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('contact@client.com')
                                    ->live(debounce: 500),

                                TextInput::make('client_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->placeholder('+1 (555) 987-6543')
                                    ->live(debounce: 500),
                            ]),
                    ])
                    ->columnSpan(1),

                Grid::make(3)
                    ->schema([
                        DatePicker::make('invoice_date')
                            ->label('Invoice Date')
                            ->required()
                            ->default(now())
                            ->native(false),

                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->default(now()->addDays(30))
                            ->native(false),

                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->default(18)
                            ->suffix('%')
                            ->live(onBlur: true),
                    ]),

                Section::make('Line Items')
                    ->schema([
                        Repeater::make('items')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('description')
                                            ->label('Description')
                                            ->required()
                                            ->placeholder('Service or product description')
                                            ->columnSpan(2)
                                            ->live(debounce: 500),

                                        TextInput::make('quantity')
                                            ->label('Quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->minValue(1)
                                            ->live(onBlur: true)
                                            ->columnSpan(1),

                                        TextInput::make('unit_price')
                                            ->label('Unit Price')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->columnSpan(1),
                                    ]),
                            ])
                            // ->defaultItems(1)
                            ->addActionLabel('Add Line Item')
                            ->reorderable()
                            ->cloneable()
                            ->deleteAction(
                                fn($action) => $action->requiresConfirmation()
                            ),
                    ]),

                Grid::make(2)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes or special instructions')
                            ->columnSpan(1)
                            ->live(debounce: 500),

                        Textarea::make('terms')
                            ->label('Payment Terms')
                            ->rows(3)
                            ->placeholder('Payment is due within 30 days')
                            ->columnSpan(1)
                            ->live(debounce: 500),
                    ]),

                // Select::make('template_id')
                //     ->label('Invoice Template')
                //     ->options(Template::active()->pluck('name', 'id'))
                //     ->default(1)
                //     ->required()
                //     ->live()
                //     ->afterStateUpdated(function ($state) {
                //         $this->selectedTemplateId = false;
                //     })

            ])
            ->statePath('data');
    }

    public function getSubtotal()
    {
        $items = $this->data['items'] ?? [];

        return collect($items)->sum(function ($item) {
            return ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
        });
    }

    public function getTaxAmount(): float
    {
        $taxRate = $this->data['tax_rate'] ?? 0;
        return $this->getSubtotal() * ($taxRate / 100);
    }

    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getTaxAmount();
    }

    public function getPreviewInvoice()
    {
        $data = $this->data;

        $invoice = new Invoice([
            'invoice_number' => 'INV-' . now()->year . '-XXXX',
            'company_name' => $data['company_name'] ?? 'Your Company',
            'company_address' => $data['company_address'] ?? null,
            'company_email' => $data['company_email'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'client_name' => $data['client_name'] ?? 'Client Name',
            'client_address' => $data['client_address'] ?? null,
            'client_email' => $data['client_email'] ?? null,
            'client_phone' => $data['client_phone'] ?? null,
            'invoice_date' => isset($data['invoice_date']) ? Carbon\Carbon::parse($data['invoice_date']) : now(),
            'due_date' => isset($data['due_date']) ? Carbon\Carbon::parse($data['due_date']) : now()->addDays(30),
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'subtotal' => $this->getSubtotal(),
            'tax_rate' => $data['tax_rate'] ?? 0,
            'tax_amount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
            'template_id' => $data['template_id'] ?? 1,
        ]);

        //set the template relationship
        $invoice->setRelation('template', Template::find($data['template_id'] ?? 1));

        // create temporary invoice items
        $items = collect($data['items'] ?? [])->map(function ($item, $index) {
            return new InvoiceItem([
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'total' => ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0),
                'sort_order' => $index,
            ]);
        });

        $invoice->setRelation('items', $items);

        return $invoice;
    }

    protected function createInvoice(): Invoice
    {
        $data = $this->data;

        $invoice = Invoice::create([
            'user_id' => Auth::id(),
            'invoice_number' => (new Invoice())->generateInvoiceNumber(),
            'company_name' => $data['company_name'],
            'company_address' => $data['company_address'] ?? null,
            'company_email' => $data['company_email'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'client_name' => $data['client_name'],
            'client_address' => $data['client_address'] ?? null,
            'client_email' => $data['client_email'] ?? null,
            'client_phone' => $data['client_phone'] ?? null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'subtotal' => $this->getSubtotal(),
            'tax_rate' => $data['tax_rate'] ?? 0,
            'tax_amount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
            'template_id' => $data['template_id'] ?? 1,
            'status' => 'draft',
        ]);

        foreach ($data['items'] ?? [] as $index => $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
                'sort_order' => $index,
            ]);
        }

        return $invoice;
    }

    protected function validateInvoiceData(): bool
    {
        $data = $this->data;

        if (empty($data['company_name']) || empty($data['client_name'])) {
            return false;
        }

        if (empty($data['invoice_date']) || empty($data['due_date'])) {
            return false;
        }

        $items = $data['items'] ?? [];
        if (empty($items)) {
            return false;
        }

        foreach ($items as $item) {
            if (empty($item['description']) || empty($item['quantity'] || empty($item['unit_price']))) {
                return false;
            }
        }

        return true;
    }

    #[On('auth-success')]
    public function handleAuthSuccess(): void
    {
        $pendingAction = session()->pull('pending_action');
        $invoiceData = session()->pull('invoice_data');

        if ($invoiceData) {
            $this->data = $invoiceData;
        }

        if ($pendingAction && $this->validateInvoiceData()) {
            if ($pendingAction === 'download') {
                $this->handleDownload();
            } elseif ($pendingAction === 'email') {
                $this->handleEmail();
            }
        }

    }

    public function with(): array
    {
        return [
            'title' => 'Create Invoice',
            'subtotal' => $this->getSubtotal(),
            'taxAmount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
            'previewInvoice' => $this->getPreviewInvoice(),
        ];
    }

    public function handleDownload(): void
    {

        if (!$this->validateInvoiceData()) {
            $this->form->validate();
            return;
        }

        if (!Auth::check()) {
            session()->put('pending_action', 'download');
            session()->put('invoice_data', $this->data);
            $this->dispatch('open-auth-modal', mode: 'register');
            return;
        }

        $invoice = $this->createInvoice();
        $this->dispatch('notify', message: 'Preparing download...');

        $this->redirect(route('invoice.download', $invoice), navigate: false);
    }

    public function handleEmail(): void
    {
        if (!$this->validateInvoiceData()) {
            $this->form->validate();
            return;
        }

        if (!Auth::check()) {
            session()->put('pending_action', 'email');
            session()->put('invoice_data', $this->data);
            $this->dispatch('open-auth-modal', mode: 'register');
            return;
        }

        $invoice = $this->createInvoice();
        $this->dispatch('notify', message: 'Sending email...');
        // validate client email exists
        if (!$invoice->client_email) {
            $this->dispatch('notify', message: 'Client email is required to send invoice');
            return;
        }

        // send mail
        Mail::to($invoice->client_email)->queue(new InvoiceMail($invoice));

        // clean up temp file
        $tempPath = storage_path('app/temp/invoice-' . $invoice->invoice_number . '.pdf');
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // update invoice status
        $invoice->update(['status' => 'sent']);

        $this->dispatch('notify', message: 'Invoice sent successfully to ' . $invoice->client_email);
    }

    public function handlePrint(): void
    {
        if (!Auth::check()) {
            session()->put('pending_action', 'print');
            session()->put('invoice_data', $this->data);
            $this->dispatch('open-auth-modal', mode: 'register');
            return;
        }

        $invoice = $this->createInvoice();

        $this->dispatch('open-print-window', url: route('invoice.print', $invoice));
    }
};
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Invoice</h1>
        <p class="text-gray-600">Fill in the details below to generate your professional invoice</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- left column: form --}}
        <div class="space-y-6 order-1 xl:order-0">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form wire:submit="save">
                    {{ $this->form }}
                </form>
            </div>

            {{-- Totals Summary --}}
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                <h3 class="text-lg font-semibold mb-4">Summary</h3>
                <div wire:loading class="text-sm text-gray-500">
                    Calculating...
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax ({{ $this->data['tax_rate'] ?? 0 }}%):</span>
                        <span class="font-semibold">${{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t">
                        <span>Total:</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6 space-y-2">
                    <button type="button" wire:click="handleDownload" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <span wire:loading.remove wire:target="handleDownload">Download PDF</span>
                        <span wire:loading wire:target="handleDownload">Processing...</span>
                    </button>
                    <button type="button" wire:click="handlePrint" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <span wire:loading.remove wire:target="handleDownload">Print Invoice</span>
                        <span wire:loading wire:target="handleDownload">Processing...</span>
                    </button>
                    <button type="button" wire:click="handleEmail" wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-lg border-2 border-gray-300 transition">
                        <span wire:loading.remove wire:target="handleEmail">Send via Email</span>
                        <span wire:loading wire:target="handleEmail">Processing...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Column: Preview --}}
        <div class="space-y-6 xl:order-0 xl:sticky xl:top-6 xl:self-start">

            {{-- Template Selector --}}
            <x-template-selector :templates="App\Models\Template::active()->get()"
                :selectedId="$this->data['template_id']" />
            {{-- preview --}}
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-3 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-700">Live Preview</h3>
                    <span class="text-xs text-gray-500">Updates as you type</span>
                </div>

                <div class="p-4 bg-gray-100 relative">
                    {{-- Loading Overlay --}}
                    <div wire:loading wire:target="data.company_name,data.client_name,data.items,data.template_id"
                        class="absolute inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center z-10 rounded">
                        <div class="bg-white rounded-lg shadow-lg px-4 py-2">
                            <span class="text-sm text-gray-600">Updating preview...</span>
                        </div>
                    </div>
                    <div class="bg-white rounded shadow-sm" style="transform: scale(0.85); transform-origin: top;">
                        <x-invoice-renderer :invoice="$previewInvoice" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6" x-data
        x-on:open-print-window.window="window.open($event.detail.url, '_blank', 'width=1024,height=768')"></div>

</div>