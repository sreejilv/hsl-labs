<?php

namespace App\Livewire\Admin;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    public LoginForm $form;
    public $captcha_answer = '';
    public $captcha_num1;
    public $captcha_num2;
    public $captcha_result;

    public function mount()
    {
        $this->generateCaptcha();
    }

    public function generateCaptcha()
    {
        $this->captcha_num1 = rand(1, 20);
        $this->captcha_num2 = rand(1, 20);
        $this->captcha_result = $this->captcha_num1 + $this->captcha_num2;
        $this->captcha_answer = '';
    }

    public function refreshCaptcha()
    {
        $this->generateCaptcha();
    }

    protected function rules()
    {
        return [
            'captcha_answer' => 'required|numeric',
        ];
    }

    protected function messages()
    {
        return [
            'captcha_answer.required' => 'Please solve the captcha.',
            'captcha_answer.numeric' => 'Please enter a valid number.',
        ];
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        // Validate captcha first
        if (empty($this->captcha_answer) || (int)$this->captcha_answer !== $this->captcha_result) {
            $this->addError('captcha_answer', 'Please solve the captcha correctly.');
            $this->generateCaptcha(); // Generate new captcha on error
            return;
        }

        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Check if user is admin, if not logout and show error
        if (!Auth::user()->hasRole('admin')) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            
            $this->addError('form.email', 'Access denied. Admin credentials required.');
            $this->generateCaptcha(); // Generate new captcha on error
            return;
        }

        $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
