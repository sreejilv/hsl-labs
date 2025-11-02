@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-user-plus me-2"></i>Add New Staff Member</h1>
                <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Staff List
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('medical.staff.store') }}" enctype="multipart/form-data" id="staffForm">
                @csrf
                
            <!-- Staff Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Staff Information</h5>
                </div>
                <div class="card-body">
                    <!-- Personal Details -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name') }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name') }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimum 8 characters</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="{{ old('date_of_birth') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Work Details -->
                    <hr class="my-4">
                    <h6 class="text-success mb-3"><i class="fas fa-briefcase me-2"></i>Work Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="staff_id" class="form-label">Staff ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="staff_id" name="staff_id" 
                                   value="{{ old('staff_id') }}" required>
                            <small class="text-muted">Unique identifier for the staff member</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   value="{{ old('position') }}" placeholder="e.g., Nurse, Technician, Administrator">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="{{ old('hire_date', date('Y-m-d')) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="shift" class="form-label">Work Shift <span class="text-danger">*</span></label>
                            <select class="form-select" id="shift" name="shift" required>
                                <option value="">Select Shift</option>
                                <option value="day" {{ old('shift', 'day') === 'day' ? 'selected' : '' }}>Day Shift</option>
                                <option value="night" {{ old('shift') === 'night' ? 'selected' : '' }}>Night Shift</option>
                                <option value="rotating" {{ old('shift') === 'rotating' ? 'selected' : '' }}>Rotating Shift</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="salary" class="form-label">Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="salary" name="salary" 
                                       value="{{ old('salary') }}" step="0.01" min="0">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>                <!-- Submit Button -->
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create Staff Member
                        </button>
                        <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('staffForm');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Clear previous validation states
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = '';
        });
        
        // Required fields validation
        const requiredFields = ['first_name', 'last_name', 'email', 'password', 'password_confirmation', 'staff_id', 'hire_date'];
        
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                showFieldError(field, `${getFieldLabel(fieldName)} is required`);
                isValid = false;
            }
        });
        
        // Email validation
        const emailField = form.querySelector('[name="email"]');
        if (emailField.value && !isValidEmail(emailField.value)) {
            showFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
        }
        
        // Phone validation
        const phoneField = form.querySelector('[name="phone"]');
        if (phoneField.value && !isValidPhone(phoneField.value)) {
            showFieldError(phoneField, 'Please enter a valid phone number');
            isValid = false;
        }
        
        // Staff ID validation
        const staffIdField = form.querySelector('[name="staff_id"]');
        if (staffIdField.value && !/^[A-Za-z0-9-_]+$/.test(staffIdField.value)) {
            showFieldError(staffIdField, 'Staff ID can only contain letters, numbers, hyphens, and underscores');
            isValid = false;
        }
        
        // Salary validation
        const salaryField = form.querySelector('[name="salary"]');
        if (salaryField.value && parseFloat(salaryField.value) < 0) {
            showFieldError(salaryField, 'Salary cannot be negative');
            isValid = false;
        }
        
        // Password validation
        const passwordField = form.querySelector('[name="password"]');
        const confirmPasswordField = form.querySelector('[name="password_confirmation"]');
        
        if (passwordField.value.length < 8) {
            showFieldError(passwordField, 'Password must be at least 8 characters long');
            isValid = false;
        }
        
        if (passwordField.value !== confirmPasswordField.value) {
            showFieldError(confirmPasswordField, 'Password confirmation does not match');
            isValid = false;
        }
        
        // Date validations
        const hireDateField = form.querySelector('[name="hire_date"]');
        if (hireDateField.value && new Date(hireDateField.value) > new Date()) {
            showFieldError(hireDateField, 'Hire date cannot be in the future');
            isValid = false;
        }
        
        const dobField = form.querySelector('[name="date_of_birth"]');
        if (dobField.value && new Date(dobField.value) > new Date()) {
            showFieldError(dobField, 'Date of birth cannot be in the future');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        } else {
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Staff Member...';
            submitBtn.disabled = true;
        }
    });
    
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
        }
    }
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function isValidPhone(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
    function getFieldLabel(fieldName) {
        const labels = {
            'first_name': 'First Name',
            'last_name': 'Last Name',
            'email': 'Email Address',
            'password': 'Password',
            'password_confirmation': 'Confirm Password',
            'staff_id': 'Staff ID',
            'hire_date': 'Hire Date'
        };
        return labels[fieldName] || fieldName;
    }
    
    // Auto-generate staff ID suggestion
    const firstNameField = form.querySelector('[name="first_name"]');
    const lastNameField = form.querySelector('[name="last_name"]');
    const staffIdField = form.querySelector('[name="staff_id"]');
    
    function generateStaffId() {
        const firstName = firstNameField.value.trim();
        const lastName = lastNameField.value.trim();
        
        if (firstName && lastName && !staffIdField.value) {
            const staffId = `${firstName.charAt(0).toUpperCase()}${lastName.charAt(0).toUpperCase()}${Date.now().toString().slice(-4)}`;
            staffIdField.value = staffId;
        }
    }
    
    firstNameField.addEventListener('blur', generateStaffId);
    lastNameField.addEventListener('blur', generateStaffId);

    // Real-time password validation
    const passwordField = form.querySelector('[name="password"]');
    const confirmPasswordField = form.querySelector('[name="password_confirmation"]');
    
    passwordField.addEventListener('input', function() {
        validatePasswordStrength(this);
    });
    
    confirmPasswordField.addEventListener('input', function() {
        validatePasswordConfirmation();
    });
    
    // Real-time email validation
    const emailField = form.querySelector('[name="email"]');
    emailField.addEventListener('blur', function() {
        if (this.value && !isValidEmail(this.value)) {
            showFieldError(this, 'Please enter a valid email address');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Real-time phone validation
    const phoneField = form.querySelector('[name="phone"]');
    phoneField.addEventListener('input', function() {
        // Format phone number as user types
        this.value = formatPhoneNumber(this.value);
    });
    
    phoneField.addEventListener('blur', function() {
        if (this.value && !isValidPhone(this.value)) {
            showFieldError(this, 'Please enter a valid phone number (e.g., +1234567890)');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Staff ID validation
    staffIdField.addEventListener('input', function() {
        // Only allow alphanumeric, hyphens, and underscores
        this.value = this.value.replace(/[^A-Za-z0-9-_]/g, '');
    });
    
    staffIdField.addEventListener('blur', function() {
        if (this.value && this.value.length < 3) {
            showFieldError(this, 'Staff ID must be at least 3 characters long');
        } else if (this.value && !/^[A-Za-z0-9-_]+$/.test(this.value)) {
            showFieldError(this, 'Staff ID can only contain letters, numbers, hyphens, and underscores');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Salary validation
    const salaryField = form.querySelector('[name="salary"]');
    salaryField.addEventListener('input', function() {
        // Only allow numbers and decimal point
        this.value = this.value.replace(/[^0-9.]/g, '');
        
        // Prevent multiple decimal points
        if ((this.value.match(/\./g) || []).length > 1) {
            this.value = this.value.slice(0, -1);
        }
    });
    
    salaryField.addEventListener('blur', function() {
        if (this.value && parseFloat(this.value) < 0) {
            showFieldError(this, 'Salary cannot be negative');
        } else if (this.value && parseFloat(this.value) > 999999) {
            showFieldError(this, 'Salary seems too high, please verify');
        } else if (this.value) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Date validation
    const hireDateField = form.querySelector('[name="hire_date"]');
    hireDateField.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        const oneYearAgo = new Date();
        oneYearAgo.setFullYear(today.getFullYear() - 1);
        
        if (selectedDate > today) {
            showFieldError(this, 'Hire date cannot be in the future');
        } else if (selectedDate < oneYearAgo) {
            showFieldError(this, 'Hire date seems too far in the past, please verify');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    const dobField = form.querySelector('[name="date_of_birth"]');
    dobField.addEventListener('change', function() {
        if (this.value) {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const age = today.getFullYear() - selectedDate.getFullYear();
            
            if (selectedDate > today) {
                showFieldError(this, 'Date of birth cannot be in the future');
            } else if (age < 16) {
                showFieldError(this, 'Staff member must be at least 16 years old');
            } else if (age > 80) {
                showFieldError(this, 'Please verify the date of birth');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        }
    });
    
    // Name validation
    const nameFields = ['first_name', 'last_name'];
    nameFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        field.addEventListener('input', function() {
            // Only allow letters, spaces, hyphens, and apostrophes
            this.value = this.value.replace(/[^A-Za-z\s\-']/g, '');
        });
        
        field.addEventListener('blur', function() {
            if (this.value && this.value.length < 2) {
                showFieldError(this, `${getFieldLabel(fieldName)} must be at least 2 characters long`);
            } else if (this.value && !/^[A-Za-z\s\-']+$/.test(this.value)) {
                showFieldError(this, `${getFieldLabel(fieldName)} can only contain letters, spaces, hyphens, and apostrophes`);
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    function validatePasswordStrength(field) {
        const password = field.value;
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        
        if (password.length === 0) {
            field.classList.remove('is-invalid', 'is-valid');
            if (feedback) feedback.textContent = '';
            return;
        }
        
        if (password.length < 8) {
            showFieldError(field, 'Password must be at least 8 characters long');
            return;
        }
        
        let strength = 0;
        const checks = [
            /[a-z]/.test(password), // lowercase
            /[A-Z]/.test(password), // uppercase
            /[0-9]/.test(password), // numbers
            /[^A-Za-z0-9]/.test(password) // special characters
        ];
        
        strength = checks.filter(Boolean).length;
        
        if (strength < 2) {
            showFieldError(field, 'Password should contain a mix of letters and numbers');
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            if (feedback) feedback.textContent = '';
        }
    }
    
    function validatePasswordConfirmation() {
        const password = passwordField.value;
        const confirmation = confirmPasswordField.value;
        
        if (confirmation.length === 0) {
            confirmPasswordField.classList.remove('is-invalid', 'is-valid');
            return;
        }
        
        if (password !== confirmation) {
            showFieldError(confirmPasswordField, 'Password confirmation does not match');
        } else {
            confirmPasswordField.classList.remove('is-invalid');
            confirmPasswordField.classList.add('is-valid');
        }
    }
    
    function formatPhoneNumber(phone) {
        // Remove all non-numeric characters except + at the beginning
        let cleaned = phone.replace(/[^\d+]/g, '');
        
        // Ensure only one + at the beginning
        if (cleaned.startsWith('+')) {
            cleaned = '+' + cleaned.substring(1).replace(/\+/g, '');
        } else {
            cleaned = cleaned.replace(/\+/g, '');
        }
        
        return cleaned;
    }
    
    // Enhanced validation functions
    function isValidPhone(phone) {
        const cleaned = phone.replace(/[\s\-\(\)]/g, '');
        // Allow international format with + and 7-15 digits
        const re = /^[\+]?[1-9]\d{6,14}$/;
        return re.test(cleaned);
    }
});
</script>

<style>
/* Enhanced form validation styles */
.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.7-.04 2.2-2.28 1.41-1.41C7.1 2.48 7.1 2.48 7.1 2.48s.69-.69.69-.69c-.01-.01-.01-.01-.01-.01L6.41 1c-.6-.6-1.24-.6-1.84 0L3.04 2.53 1.7 3.88l.6.85zM.27 5.23l1.35-1.35L2.3 3.2l-.03.33s-.59.6-.59.6l.29.28z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-select.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.7-.04 2.2-2.28 1.41-1.41C7.1 2.48 7.1 2.48 7.1 2.48s.69-.69.69-.69c-.01-.01-.01-.01-.01-.01L6.41 1c-.6-.6-1.24-.6-1.84 0L3.04 2.53 1.7 3.88l.6.85zM.27 5.23l1.35-1.35L2.3 3.2l-.03.33s-.59.6-.59.6l.29.28z'/%3e%3c/svg%3e");
    background-position: right 0.75rem center, center right 2.25rem;
    background-size: 16px 12px, calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center, center right 2.25rem;
    background-size: 16px 12px, calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.valid-feedback {
    display: block;
    color: #28a745;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Loading button animation */
#submitBtn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Password strength indicator */
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 0.25rem;
    background-color: #e9ecef;
    transition: all 0.3s ease;
}

.password-strength.weak {
    background-color: #dc3545;
    width: 25%;
}

.password-strength.fair {
    background-color: #ffc107;
    width: 50%;
}

.password-strength.good {
    background-color: #28a745;
    width: 75%;
}

.password-strength.strong {
    background-color: #28a745;
    width: 100%;
}

/* Field focus animation */
.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

/* Required field indicator */
.form-label span.text-danger {
    font-weight: bold;
}

/* Card hover effect */
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

/* Button loading state */
.btn.loading {
    position: relative;
    pointer-events: none;
}

.btn.loading .fas {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    
    .card-header h5 {
        font-size: 1.1rem;
    }
}
</style>
@endsection