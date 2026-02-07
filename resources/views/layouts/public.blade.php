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
                        <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="text-gray-700 hover:text-gray-900 font-medium">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900">
                                Sign Out
                            </button>
                        </form>
                    @else
                        <button type="button" onclick="Livewire.dispatch('open-auth-modal', { mode: 'login' })"
                            class="text-gray-700 hover:text-gray-900 font-medium">
                            Sign In
                        </button>
                        <button type="button" onclick="Livewire.dispatch('open-auth-modal', { mode: 'register' })"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                            Sign Up
                        </button>
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
    <div x-data="{ 
            show: false, 
            message: '',
            showNotification(msg) {
                this.message = msg;
                this.show = true;
                setTimeout(() => this.show = false, 3000);
            }
        }" x-on:notify.window="showNotification($event.detail.message)" x-show="show" x-transition
        class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50"
        style="display: none;" x-text="message">
    </div>
</body>

</html>