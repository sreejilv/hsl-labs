<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Check if user is medical staff (surgeon or staff), if not logout and show error
        if (!Auth::user()->hasRole(['surgeon', 'staff'])) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            $this->addError('form.email', 'Access denied. Medical staff credentials required.');
            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center mb-4">
        <h3 class="text-success mb-2">
            <i class="fas fa-user-md me-2"></i>Medical Portal
        </h3>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <x-input-error :messages="$errors->get('form.email')" class="mt-1 mb-2" />
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input wire:model="form.email" id="email" 
                       class="form-control @error('form.email') is-invalid @enderror" 
                       type="email" name="email" 
                       placeholder="Enter your email" required autofocus autocomplete="username" />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input wire:model="form.password" id="password" 
                       class="form-control @error('form.password') is-invalid @enderror"
                       type="password" name="password" placeholder="Enter password"
                       required autocomplete="current-password" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </div>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
            <div class="text-center mb-3">
                <a class="text-decoration-none small" href="{{ route('password.request') }}" wire:navigate>
                    Forgot your password?
                </a>
            </div>
        @endif
    </form>
    
    <!-- Admin Link -->
    {{-- <hr class="my-3">
    <div class="text-center">
        <small class="text-muted">
            Administrator? <a href="{{ route('admin.login') }}" wire:navigate class="text-decoration-none">Login here</a>
        </small>
    </div> --}}
</div>
