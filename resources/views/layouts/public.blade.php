<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Invoice Generator' }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
    @livewireStyles
</head>

<body class="bg-gray-50 antialiased">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">Invoice Generator</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-gray-700 hover:text-gray-900">
                            Dashboard
                        </a>
                    @else
                        <a href="#" class="text-gray-700 hover:text-gray-900">
                            Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @filamentScripts
    @livewireScripts
</body>

</html>