<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <div class="text-center mb-4">
        <h3 class="text-info mb-2">
            <i class="fas fa-key me-2"></i>Reset Password
        </h3>
    </div>

    <div class="mb-4">
        <p class="text-muted small text-center">
            Forgot your password? No problem. Just enter your email address and we'll send you a password reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input wire:model="email" id="email" class="form-control" type="email" name="email" 
                       placeholder="Enter your email address" required autofocus />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-info btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
            </button>
        </div>
    </form>

    <!-- Back to Login -->
    <hr class="my-3">
    <div class="text-center">
        <small class="text-muted">
            Remember your password? 
            <a href="{{ route('login') }}" wire:navigate class="text-decoration-none">Back to Login</a>
        </small>
    </div>
</div>
