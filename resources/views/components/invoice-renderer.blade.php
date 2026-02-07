@props(['invoice', 'forPdf' => false])

@switch($invoice->template->slug)
    @case('modern-minimalist')
        <x-templates.modern-minimalist :invoice="$invoice" :forPdf="$forPdf" />
        @break
    @case('classic-business')
        <x-templates.classic-business :invoice="$invoice" :forPdf="$forPdf" />
        @break
    @case('creative-agency')
        <x-templates.creative-agency :invoice="$invoice" :forPdf="$forPdf" />
        @break

    @case('corporate-blue')
        {{-- we'll create this next --}}
        <x-templates.classic-business :invoice="$invoice" :forPdf="$forPdf" />
        @break

    @default
        <x-templates.modern-minimalist :invoice="$invoice" :forPdf="$forPdf" />
@endswitch