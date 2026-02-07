<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
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

new #[Layout('layouts.public')] class extends Component implements HasActions, HasSchemas {
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];
    public ?int $selectedTemplateId = 1;

    public function mount(): void
    {
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
                                    ->placeholder('Your Company Name'),

                                Textarea::make('company_address')
                                    ->label('Address')
                                    ->rows(3)
                                    ->placeholder('123 Business Street, City, Country'),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('company_email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('hello@company.com'),

                                        TextInput::make('company_phone')
                                            ->label('Phone')
                                            ->tel()
                                            ->placeholder('+1 (555) 123-4567'),
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
                            ->placeholder('Client Company Name'),

                        Textarea::make('client_address')
                            ->label('Address')
                            ->rows(3)
                            ->placeholder('456 Client Avenue, City, Country'),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('client_email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('contact@client.com'),

                                TextInput::make('client_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->placeholder('+1 (555) 987-6543'),
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
                                            ->columnSpan(2),

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
                            ->columnSpan(1),

                        Textarea::make('terms')
                            ->label('Payment Terms')
                            ->rows(3)
                            ->placeholder('Payment is due within 30 days')
                            ->columnSpan(1),
                    ]),

                Select::make('template_id')
                    ->label('Invoice Template')
                    ->options(Template::active()->pluck('name', 'id'))
                    ->default(1)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedTemplateId = false;
                    })

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

    public function with(): array
    {
        return [
            'title' => 'Create Invoice',
            'subtotal' => $this->getSubtotal(),
            'taxAmount' => $this->getTaxAmount(),
            'total' => $this->getTotal(),
        ];
    }
};
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Invoice</h1>
        <p class="text-gray-600">Fill in the details below to generate your professional invoice</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- left column: form --}}
        <div class="space-y-6">
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
                    <button type="button" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        <span wire:loading.remove>Download PDF</span>
                        <span wire:loading>Processing...</span>
                    </button>
                    <button 
                        type="button" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-4 rounded-lg border-2 border-gray-300 transition">
                        <span wire:loading.remove>Send via Email</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right column: Preview --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-center py-8 text-gray-500">
                    <p class="text-lg font-semibold mb-2">Live Preview</p>
                    <p class="text-sm">Preview will appear here as you fill the form</p>
                </div>
            </div>
        </div>
    </div>
</div>