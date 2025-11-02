@extends('layouts.medical')

@section('title', 'Add New Patient')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Add New Patient
                    </h4>
                </div>
                <div class="card-body">
                    <form id="patientForm" action="{{ route('medical.patients.store') }}" method="POST" novalidate>
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-user me-1"></i>Personal Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                       required minlength="2" maxlength="255">
                                <div class="invalid-feedback" id="first_name_error">
                                    @error('first_name'){{ $message }}@else Please enter a valid first name (2-255 characters).@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                       required minlength="2" maxlength="255">
                                <div class="invalid-feedback" id="last_name_error">
                                    @error('last_name'){{ $message }}@else Please enter a valid last name (2-255 characters).@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" maxlength="255">
                                <div class="invalid-feedback" id="email_error">
                                    @error('email'){{ $message }}@else Please enter a valid email address.@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       required pattern="[0-9+\-\s\(\)]{10,20}" maxlength="20">
                                <div class="invalid-feedback" id="phone_error">
                                    @error('phone'){{ $message }}@else Please enter a valid phone number (10-20 digits).@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                                       required max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                                <div class="invalid-feedback" id="date_of_birth_error">
                                    @error('date_of_birth'){{ $message }}@else Please enter a valid date of birth (must be in the past).@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <div class="invalid-feedback" id="gender_error">
                                    @error('gender'){{ $message }}@else Please select a gender.@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select class="form-select @error('blood_group') is-invalid @enderror" id="blood_group" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                </select>
                                <div class="invalid-feedback" id="blood_group_error">
                                    @error('blood_group'){{ $message }}@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" required minlength="10" maxlength="500">{{ old('address') }}</textarea>
                                <div class="invalid-feedback" id="address_error">
                                    @error('address'){{ $message }}@else Please enter a complete address (10-500 characters).@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-phone me-1"></i>Emergency Contact
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="emergency_contact_name" class="form-label">Contact Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" 
                                       required minlength="2" maxlength="255">
                                <div class="invalid-feedback" id="emergency_contact_name_error">
                                    @error('emergency_contact_name'){{ $message }}@else Please enter a valid contact name (2-255 characters).@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label">Contact Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                       id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" 
                                       required pattern="[0-9+\-\s\(\)]{10,20}" maxlength="20">
                                <div class="invalid-feedback" id="emergency_contact_phone_error">
                                    @error('emergency_contact_phone'){{ $message }}@else Please enter a valid emergency contact phone (10-20 digits).@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-notes-medical me-1"></i>Medical Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control @error('allergies') is-invalid @enderror" 
                                          id="allergies" name="allergies" rows="3" maxlength="1000"
                                          placeholder="List any known allergies...">{{ old('allergies') }}</textarea>
                                <div class="invalid-feedback" id="allergies_error">
                                    @error('allergies'){{ $message }}@else Maximum 1000 characters allowed.@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="medical_history" class="form-label">Medical History</label>
                                <textarea class="form-control @error('medical_history') is-invalid @enderror" 
                                          id="medical_history" name="medical_history" rows="3" maxlength="1000"
                                          placeholder="Previous medical conditions, surgeries, etc...">{{ old('medical_history') }}</textarea>
                                <div class="invalid-feedback" id="medical_history_error">
                                    @error('medical_history'){{ $message }}@else Maximum 1000 characters allowed.@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="current_medications" class="form-label">Current Medications</label>
                                <textarea class="form-control @error('current_medications') is-invalid @enderror" 
                                          id="current_medications" name="current_medications" rows="3" maxlength="1000"
                                          placeholder="List current medications...">{{ old('current_medications') }}</textarea>
                                <div class="invalid-feedback" id="current_medications_error">
                                    @error('current_medications'){{ $message }}@else Maximum 1000 characters allowed.@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Insurance Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-shield-alt me-1"></i>Insurance Information
                                </h5>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="insurance_provider" class="form-label">Insurance Provider</label>
                                <input type="text" class="form-control @error('insurance_provider') is-invalid @enderror" 
                                       id="insurance_provider" name="insurance_provider" value="{{ old('insurance_provider') }}" 
                                       maxlength="255">
                                <div class="invalid-feedback" id="insurance_provider_error">
                                    @error('insurance_provider'){{ $message }}@else Maximum 255 characters allowed.@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="insurance_policy_number" class="form-label">Policy Number</label>
                                <input type="text" class="form-control @error('insurance_policy_number') is-invalid @enderror" 
                                       id="insurance_policy_number" name="insurance_policy_number" value="{{ old('insurance_policy_number') }}" 
                                       maxlength="255">
                                <div class="invalid-feedback" id="insurance_policy_number_error">
                                    @error('insurance_policy_number'){{ $message }}@else Maximum 255 characters allowed.@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : 'selected' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="status_error">
                                    @error('status'){{ $message }}@else Please select a status.@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" id="submitBtn" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Save Patient
                                    </button>
                                    <a href="{{ route('medical.patients.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('patientForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Field validation rules
    const validationRules = {
        first_name: { required: true, minLength: 2, maxLength: 255, pattern: /^[A-Za-z\s'-]+$/ },
        last_name: { required: true, minLength: 2, maxLength: 255, pattern: /^[A-Za-z\s'-]+$/ },
        email: { required: false, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
        phone: { required: true, pattern: /^[0-9+\-\s\(\)]{10,20}$/ },
        date_of_birth: { required: true, type: 'date' },
        gender: { required: true },
        address: { required: true, minLength: 10, maxLength: 500 },
        emergency_contact_name: { required: true, minLength: 2, maxLength: 255, pattern: /^[A-Za-z\s'-]+$/ },
        emergency_contact_phone: { required: true, pattern: /^[0-9+\-\s\(\)]{10,20}$/ },
        status: { required: true }
    };

    // Real-time validation for each field
    Object.keys(validationRules).forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            field.addEventListener('input', () => validateField(fieldName));
            field.addEventListener('blur', () => validateField(fieldName));
        }
    });

    // Email field special handling
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('input', () => {
            if (emailField.value.trim() !== '') {
                validateField('email');
            } else {
                clearFieldError('email');
            }
        });
    }

    function validateField(fieldName) {
        const field = document.getElementById(fieldName);
        const rules = validationRules[fieldName];
        const value = field.value.trim();
        
        let isValid = true;
        let errorMessage = '';

        // Required field validation
        if (rules.required && !value) {
            isValid = false;
            errorMessage = `${getFieldLabel(fieldName)} is required.`;
        }
        // Email validation (only if not empty for optional fields)
        else if (fieldName === 'email' && value && !rules.pattern.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address.';
        }
        // Pattern validation
        else if (value && rules.pattern && !rules.pattern.test(value)) {
            isValid = false;
            if (fieldName.includes('phone')) {
                errorMessage = 'Please enter a valid phone number (10-20 digits).';
            } else if (fieldName.includes('name')) {
                errorMessage = 'Please enter a valid name (letters, spaces, hyphens, and apostrophes only).';
            }
        }
        // Length validation
        else if (value && rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = `${getFieldLabel(fieldName)} must be at least ${rules.minLength} characters.`;
        }
        else if (value && rules.maxLength && value.length > rules.maxLength) {
            isValid = false;
            errorMessage = `${getFieldLabel(fieldName)} must not exceed ${rules.maxLength} characters.`;
        }
        // Date validation
        else if (rules.type === 'date' && value) {
            const inputDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (inputDate >= today) {
                isValid = false;
                errorMessage = 'Date of birth must be in the past.';
            }
        }

        // Update field appearance
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            clearFieldError(fieldName);
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            showFieldError(fieldName, errorMessage);
        }

        return isValid;
    }

    function validateForm() {
        let isFormValid = true;
        
        Object.keys(validationRules).forEach(fieldName => {
            if (!validateField(fieldName)) {
                isFormValid = false;
            }
        });

        return isFormValid;
    }

    function showFieldError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearFieldError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    function getFieldLabel(fieldName) {
        const labels = {
            first_name: 'First name',
            last_name: 'Last name',
            email: 'Email',
            phone: 'Phone',
            date_of_birth: 'Date of birth',
            gender: 'Gender',
            address: 'Address',
            emergency_contact_name: 'Emergency contact name',
            emergency_contact_phone: 'Emergency contact phone',
            status: 'Status'
        };
        return labels[fieldName] || fieldName;
    }

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
            submitBtn.disabled = true;
            
            // Submit the form
            form.submit();
        } else {
            // Scroll to first error
            const firstInvalidField = form.querySelector('.is-invalid');
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
        }
    });

    // Character count for textareas
    ['allergies', 'medical_history', 'current_medications', 'address'].forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field) {
            const maxLength = field.getAttribute('maxlength');
            if (maxLength) {
                // Create character counter
                const counter = document.createElement('small');
                counter.className = 'text-muted';
                counter.id = `${fieldName}_counter`;
                field.parentNode.appendChild(counter);
                
                function updateCounter() {
                    const remaining = maxLength - field.value.length;
                    counter.textContent = `${field.value.length}/${maxLength} characters`;
                    counter.className = remaining < 50 ? 'text-danger' : 'text-muted';
                }
                
                field.addEventListener('input', updateCounter);
                updateCounter(); // Initialize
            }
        }
    });
});
</script>

@endsection