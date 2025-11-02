<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HSL Labs') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                font-family: 'Figtree', sans-serif;
            }
            .login-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .logo-container {
                text-align: center;
                margin-bottom: 2rem;
            }
            .app-logo {
                width: 60px;
                height: 60px;
                margin: 0 auto 1rem;
                background: #667eea;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 24px;
                font-weight: bold;
            }
            .is-invalid {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            .is-valid {
                border-color: #198754 !important;
                box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
            }
            .invalid-feedback {
                display: block;
                color: #dc3545;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
            .valid-feedback {
                display: block;
                color: #198754;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
            .feedback-message {
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }
            .feedback-message.invalid-feedback {
                color: #dc3545;
            }
            .feedback-message.valid-feedback {
                color: #198754;
            }
            .captcha-card {
                background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
                border: 2px solid #dee2e6;
                border-radius: 8px;
            }
            .captcha-question {
                font-family: 'Courier New', monospace;
                font-weight: bold;
                color: #495057;
                text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Email validation regex
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                // Debounce function to limit validation calls
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }
                
                // Clear client-side validation when server-side errors appear
                function clearClientValidation() {
                    const feedbackElements = document.querySelectorAll('.feedback-message');
                    feedbackElements.forEach(el => el.remove());
                }
                
                // Listen for Livewire updates to clear client validation
                document.addEventListener('livewire:updated', clearClientValidation);
                
                // Get all forms
                const forms = document.querySelectorAll('form');
                
                forms.forEach(function(form) {
                    const emailInput = form.querySelector('input[type="email"]');
                    const passwordInput = form.querySelector('input[type="password"]');
                    const captchaInput = form.querySelector('input[name="captcha"]');
                    const submitButton = form.querySelector('button[type="submit"]');
                    
                    if (emailInput) {
                        const debouncedEmailValidation = debounce(() => {
                            validateEmail(emailInput);
                            updateSubmitButton(form);
                        }, 300);
                        
                        emailInput.addEventListener('input', debouncedEmailValidation);
                        
                        emailInput.addEventListener('blur', function() {
                            validateEmail(this);
                            updateSubmitButton(form);
                        });
                    }
                    
                    if (passwordInput) {
                        const debouncedPasswordValidation = debounce(() => {
                            validatePassword(passwordInput);
                            updateSubmitButton(form);
                        }, 300);
                        
                        passwordInput.addEventListener('input', debouncedPasswordValidation);
                        
                        passwordInput.addEventListener('blur', function() {
                            validatePassword(this);
                            updateSubmitButton(form);
                        });
                    }
                    
                    if (captchaInput) {
                        captchaInput.addEventListener('input', function() {
                            validateCaptcha(this);
                            updateSubmitButton(form);
                        });
                        
                        captchaInput.addEventListener('blur', function() {
                            validateCaptcha(this);
                            updateSubmitButton(form);
                        });
                    }
                    
                    // Form submission validation
                    form.addEventListener('submit', function(e) {
                        let isValid = true;
                        
                        if (emailInput && !validateEmail(emailInput)) {
                            isValid = false;
                        }
                        
                        if (passwordInput && !validatePassword(passwordInput)) {
                            isValid = false;
                        }
                        
                        if (captchaInput && !validateCaptcha(captchaInput)) {
                            isValid = false;
                        }
                        
                        if (!isValid) {
                            e.preventDefault();
                            showAlert('Please correct the errors before submitting.', 'danger');
                        }
                    });
                });
                
                function validateEmail(input) {
                    const value = input.value.trim();
                    const feedback = getOrCreateFeedback(input);
                    
                    // Remove previous validation classes
                    input.classList.remove('is-valid', 'is-invalid');
                    
                    if (value === '') {
                        // Don't show client validation for empty field if server error exists
                        if (feedback) {
                            input.classList.add('is-invalid');
                            feedback.textContent = 'Email address is required.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else if (!emailRegex.test(value)) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Please enter a valid email address.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else {
                        input.classList.add('is-valid');
                        // Remove success message - just add valid class
                        if (feedback) {
                            feedback.remove();
                        }
                        return true;
                    }
                }
                
                function validatePassword(input) {
                    const value = input.value;
                    const feedback = getOrCreateFeedback(input);
                    
                    // Remove previous validation classes
                    input.classList.remove('is-valid', 'is-invalid');
                    
                    if (value === '') {
                        // Don't show client validation for empty field if server error exists
                        if (feedback) {
                            input.classList.add('is-invalid');
                            feedback.textContent = 'Password is required.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else if (value.length < 6) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Password must be at least 6 characters long.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else {
                        input.classList.add('is-valid');
                        // Remove success message - just add valid class
                        if (feedback) {
                            feedback.remove();
                        }
                        return true;
                    }
                }
                
                function validateCaptcha(input) {
                    const value = input.value.trim();
                    const feedback = getOrCreateFeedback(input);
                    
                    // Remove previous validation classes
                    input.classList.remove('is-valid', 'is-invalid');
                    
                    if (value === '') {
                        // Don't show client validation for empty field if server error exists
                        if (feedback) {
                            input.classList.add('is-invalid');
                            feedback.textContent = 'Please solve the captcha.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else if (!/^\d+$/.test(value)) {
                        input.classList.add('is-invalid');
                        if (feedback) {
                            feedback.textContent = 'Please enter a valid number.';
                            feedback.className = 'feedback-message invalid-feedback';
                        }
                        return false;
                    } else {
                        input.classList.add('is-valid');
                        // Remove success message - just add valid class
                        if (feedback) {
                            feedback.remove();
                        }
                        return true;
                    }
                }
                
                function getOrCreateFeedback(input) {
                    const inputContainer = input.closest('.mb-3');
                    
                    // Remove any existing client-side feedback elements for this input
                    const existingFeedback = inputContainer.querySelectorAll('.feedback-message');
                    existingFeedback.forEach(el => el.remove());
                    
                    // Check if there are server-side error messages (Livewire errors)
                    const serverErrors = inputContainer.querySelectorAll('.text-danger:not(.feedback-message)');
                    
                    // If server-side errors exist and input has content, allow client validation to override
                    if (serverErrors.length > 0 && input.value.trim() === '') {
                        return null; // Don't show client validation for empty fields with server errors
                    }
                    
                    // Create new feedback element
                    const feedback = document.createElement('div');
                    feedback.className = 'feedback-message mt-1';
                    
                    // Insert after the input group
                    const inputGroup = input.closest('.input-group');
                    if (inputGroup) {
                        inputGroup.parentNode.insertBefore(feedback, inputGroup.nextSibling);
                    } else {
                        input.parentNode.appendChild(feedback);
                    }
                    
                    return feedback;
                }
                
                function updateSubmitButton(form) {
                    const submitButton = form.querySelector('button[type="submit"]');
                    const emailInput = form.querySelector('input[type="email"]');
                    const passwordInput = form.querySelector('input[type="password"]');
                    const captchaInput = form.querySelector('input[name="captcha"]');
                    
                    if (submitButton) {
                        let allValid = true;
                        
                        // Check email field
                        if (emailInput) {
                            const emailValid = emailInput.classList.contains('is-valid') || 
                                             (emailInput.value.trim() !== '' && !emailInput.classList.contains('is-invalid'));
                            if (!emailValid) allValid = false;
                        }
                        
                        // Check password field
                        if (passwordInput) {
                            const passwordValid = passwordInput.classList.contains('is-valid') || 
                                                 (passwordInput.value.trim() !== '' && !passwordInput.classList.contains('is-invalid'));
                            if (!passwordValid) allValid = false;
                        }
                        
                        // Check captcha field
                        if (captchaInput) {
                            const captchaValid = captchaInput.classList.contains('is-valid') || 
                                                (captchaInput.value.trim() !== '' && !captchaInput.classList.contains('is-invalid'));
                            if (!captchaValid) allValid = false;
                        }
                        
                        // Enable/disable submit button
                        if (allValid) {
                            submitButton.disabled = false;
                            submitButton.classList.remove('btn-secondary');
                            submitButton.classList.add(submitButton.dataset.originalClass || 'btn-primary');
                        } else {
                            submitButton.disabled = true;
                            if (!submitButton.dataset.originalClass) {
                                const buttonClasses = submitButton.className.match(/btn-(primary|success|info|warning|danger)/);
                                submitButton.dataset.originalClass = buttonClasses ? buttonClasses[0] : 'btn-primary';
                            }
                            submitButton.classList.remove('btn-primary', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger');
                            submitButton.classList.add('btn-secondary');
                        }
                    }
                }
                
                function showAlert(message, type) {
                    // Remove existing alerts
                    const existingAlert = document.querySelector('.js-alert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    
                    // Create new alert
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${type} alert-dismissible fade show js-alert`;
                    alert.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    
                    // Insert at the top of the form container
                    const formContainer = document.querySelector('.login-card');
                    formContainer.insertBefore(alert, formContainer.firstChild);
                    
                    // Auto dismiss after 5 seconds
                    setTimeout(() => {
                        if (alert) {
                            alert.remove();
                        }
                    }, 5000);
                }
            });
        </script>
    </head>
    <body>
        <div class="login-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="login-card p-4">
                            <div class="logo-container">
                                <div class="app-logo">
                                    HSL
                                </div>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
