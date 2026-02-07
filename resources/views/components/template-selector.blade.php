@props(['templates', 'selectedId' => 1])

<div class="bg-white rounded-lg shadow-sm p-6" x-data="{ selectedTemplate: {{ $selectedId }} }">
    <h3 class="font-semibold text-gray-900 mb-4">Choose Template</h3>
    
    <div class="grid grid-cols-2 gap-4">
        @foreach($templates as $template)
            <button 
                type="button"
                wire:click="$set('data.template_id', {{ $template->id }})"
                x-on:click="selectedTemplate = {{ $template->id }}"
                class="relative group cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-200"
                x-bind:class="selectedTemplate === {{ $template->id }} 
                    ? 'border-blue-600 shadow-lg' 
                    : 'border-gray-200 hover:border-gray-300'"
            >
                {{-- Template Preview Image Placeholder --}}
                <div class="bg-linear-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                    <div class="text-center p-4">
                        <div class="text-2xl mb-2">
                            @switch($template->slug)
                                @case('modern-minimalist')
                                    📄
                                    @break
                                @case('classic-business')
                                    📋
                                    @break
                                @case('creative-agency')
                                    🎨
                                    @break
                                @default
                                    📃
                            @endswitch
                        </div>
                        <div class="text-xs font-semibold text-gray-600">
                            {{ $template->name }}
                        </div>
                    </div>
                </div>
                
                {{-- Selected Indicator --}}
                <div 
                    x-show="selectedTemplate === {{ $template->id }}"
                    x-transition
                    class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>
        @endforeach
    </div>
    
    <p class="text-xs text-gray-500 mt-4 text-center">
        Click a template to preview
    </p>
</div>