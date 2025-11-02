@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-user-edit me-2"></i>Edit Staff Member</h1>
                <div>
                    <a href="{{ route('medical.staff.show', $staff) }}" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('medical.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Staff List
                    </a>
                </div>
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

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('medical.staff.update', $staff) }}" enctype="multipart/form-data" id="staffForm">
                @csrf
                @method('PUT')
                
            <!-- Staff Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Staff Information</h5>
                </div>
                <div class="card-body">
                    <!-- Personal Details -->
                    <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Personal Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name', $staff->user->first_name) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name', $staff->user->last_name) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', $staff->user->email) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', $staff->user->phone) }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="{{ old('date_of_birth', $staff->user->date_of_birth?->format('Y-m-d')) }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $staff->user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $staff->user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $staff->user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $staff->user->address) }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Work Details -->
                    <hr class="my-4">
                    <h6 class="text-success mb-3"><i class="fas fa-briefcase me-2"></i>Work Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="staff_id" class="form-label">Staff ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="staff_id" name="staff_id" 
                                   value="{{ old('staff_id', $staff->staff_id) }}" required>
                            <small class="text-muted">Unique identifier for the staff member</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   value="{{ old('position', $staff->position) }}" placeholder="e.g., Nurse, Technician, Administrator">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="{{ old('hire_date', $staff->hire_date?->format('Y-m-d')) }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="shift" class="form-label">Work Shift</label>
                            <select class="form-select" id="shift" name="shift">
                                <option value="">Select Shift</option>
                                <option value="day" {{ old('shift', $staff->shift) === 'day' ? 'selected' : '' }}>Day Shift</option>
                                <option value="night" {{ old('shift', $staff->shift) === 'night' ? 'selected' : '' }}>Night Shift</option>
                                <option value="rotating" {{ old('shift', $staff->shift) === 'rotating' ? 'selected' : '' }}>Rotating Shift</option>
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
                                       value="{{ old('salary', $staff->salary) }}" step="0.01" min="0">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $staff->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $staff->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>                <!-- Change Password Card -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Change Password (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields empty if you don't want to change the password.
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Minimum 8 characters</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save me-2"></i>Update Staff Member
                        </button>
                        <a href="{{ route('medical.staff.show', $staff) }}" class="btn btn-outline-info btn-lg me-3">
                            <i class="fas fa-eye me-2"></i>View Details
                        </a>
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
        const requiredFields = ['first_name', 'last_name', 'email', 'staff_id', 'hire_date'];
        
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
        
        if (passwordField.value || confirmPasswordField.value) {
            if (passwordField.value.length < 8) {
                showFieldError(passwordField, 'Password must be at least 8 characters long');
                isValid = false;
            }
            
            if (passwordField.value !== confirmPasswordField.value) {
                showFieldError(confirmPasswordField, 'Password confirmation does not match');
                isValid = false;
            }
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
            'staff_id': 'Staff ID',
            'hire_date': 'Hire Date'
        };
        return labels[fieldName] || fieldName;
    }
});
</script>
@endsection