<div>
    <div class="text-center mb-4">
        <h3 class="text-primary mb-2">
            <i class="fas fa-user-shield me-2"></i>Admin Access
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
                       placeholder="Enter admin email" required autofocus autocomplete="username" />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <x-input-error :messages="$errors->get('form.password')" class="mt-1 mb-2" />
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input wire:model="form.password" id="password" 
                       class="form-control @error('form.password') is-invalid @enderror"
                       type="password" name="password" placeholder="Enter password"
                       required autocomplete="current-password" />
            </div>
        </div>

        <!-- Captcha -->
        <div class="mb-3">
            <label for="captcha" class="form-label">Security Check <small class="text-muted">(solve the math problem)</small></label>
            <x-input-error :messages="$errors->get('captcha_answer')" class="mt-1 mb-2" />
            
            <!-- Math Question and Refresh Button Row -->
            <div class="row align-items-center mb-2">
                <div class="col-10">
                    <div class="card bg-light">
                        <div class="card-body text-center py-2">
                            <strong class="text-primary fs-5">{{ $captcha_num1 }} + {{ $captcha_num2 }} = ?</strong>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <button type="button" wire:click="refreshCaptcha" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <!-- Input Field Row -->
            <div class="row">
                <div class="col-12">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                        <input wire:model="captcha_answer" id="captcha" 
                               class="form-control @error('captcha_answer') is-invalid @enderror" 
                               type="number" name="captcha" placeholder="Enter your answer"
                               required autocomplete="off" />
                    </div>
                </div>
            </div>
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
            <button type="submit" class="btn btn-primary btn-lg">
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
    
    <!-- Medical Staff Link -->
    <hr class="my-3">
    <div class="text-center">
        <small class="text-muted">
            Medical staff? <a href="{{ route('login') }}" wire:navigate class="text-decoration-none">Login here</a>
        </small>
    </div>
</div>
