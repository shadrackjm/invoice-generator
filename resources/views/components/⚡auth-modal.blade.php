<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

new class extends Component
{
   public bool $show = false;
    public string $mode = 'register'; // 'login' or 'register'
    
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $remember = true;
    public bool $terms = false;
    
    public function mount(): void
    {
        $this->show = false;
    }
    
    #[On('open-auth-modal')]
    public function open(string $mode = 'register'): void
    {
        $this->mode = $mode;
        $this->show = true;
        $this->resetForm();
    }
    
    public function close(): void
    {
        $this->show = false;
        $this->resetForm();
    }
    
    public function switchMode(): void
    {
        $this->mode = $this->mode === 'login' ? 'register' : 'login';
        $this->resetForm();
    }
    
    protected function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'terms']);
        $this->resetValidation();
    }
    
    public function login(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            
            $this->dispatch('notify', message: 'Welcome back, ' . Auth::user()->name . '!');
            $this->dispatch('auth-success');
            $this->close();
        } else {
            $this->addError('email', 'The provided credentials do not match our records.');
        }
    }
    
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must accept the Terms of Service.',
        ]);
        
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        
        Auth::login($user, true);
        session()->regenerate();
        
        $this->dispatch('notify', message: 'Account created successfully!');
        $this->dispatch('auth-success');
        $this->close();
    }
}; ?>

<div>
    {{-- Modal Backdrop --}}
    <div 
        x-data="{ show: @entangle('show').live }"
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50"
        x-on:click="$wire.close()"
        style="display: none;"
    >
    </div>
    
    {{-- Modal Content --}}
    <div 
        x-data="{ show: @entangle('show').live }"
        x-show="show"
        x-transition
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                class="bg-white rounded-lg shadow-xl max-w-md w-full p-6"
                x-on:click.stop
            >
                {{-- Header --}}
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ $mode === 'login' ? 'Welcome Back' : 'Create Account' }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $mode === 'login' ? 'Sign in to download your invoice' : 'Create an account to save and download your invoice' }}
                        </p>
                    </div>
                    <button 
                        type="button"
                        wire:click="close"
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Forms --}}
                @if($mode === 'login')
                    {{-- Login Form --}}
                    <form wire:submit="login" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input 
                                type="email"
                                wire:model="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="your@email.com"
                                required
                                autofocus
                            >
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <input 
                                type="password"
                                wire:model="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="••••••••"
                                required
                            >
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="checkbox"
                                wire:model="remember"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                id="remember"
                            >
                            <label for="remember" class="ml-2 text-sm text-gray-600">
                                Remember me
                            </label>
                        </div>
                        
                        <button 
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                        >
                            <span wire:loading.remove>Sign In</span>
                            <span wire:loading>Signing in...</span>
                        </button>
                    </form>
                @else
                    {{-- Registration Form --}}
                    <form wire:submit="register" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name
                            </label>
                            <input 
                                type="text"
                                wire:model="name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="John Doe"
                                required
                                autofocus
                            >
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input 
                                type="email"
                                wire:model="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="your@email.com"
                                required
                            >
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <input 
                                type="password"
                                wire:model="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="••••••••"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Confirm Password
                            </label>
                            <input 
                                type="password"
                                wire:model="password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="••••••••"
                                required
                            >
                        </div>
                        
                        <div class="flex items-start">
                            <input 
                                type="checkbox"
                                wire:model="terms"
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                id="terms"
                                required
                            >
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-700">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-700">Privacy Policy</a>
                            </label>
                        </div>
                        @error('terms')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <button 
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                        >
                            <span wire:loading.remove>Create Account</span>
                            <span wire:loading>Creating account...</span>
                        </button>
                    </form>
                @endif
                
                {{-- Mode Switch --}}
                <div class="mt-6 text-center">
                    <button 
                        type="button"
                        wire:click="switchMode"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        {{ $mode === 'login' ? "Don't have an account? Sign up" : 'Already have an account? Sign in' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>