<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-md me-2"></i>Register New Doctor</h4>
                    </div>
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form wire:submit="register" id="doctorRegistrationForm" novalidate>
                            <div class="row">
                                <!-- Clinic Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="clinic_name" class="form-label">Clinic Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-clinic-medical"></i></span>
                                        <input wire:model="clinic_name" type="text" class="form-control @error('clinic_name') is-invalid @enderror" 
                                               id="clinic_name" placeholder="Enter clinic name" required minlength="3" maxlength="255">
                                        <div class="invalid-feedback" id="clinic_name_error" style="display: none;">
                                            Clinic name is required and must be at least 3 characters.
                                        </div>
                                    </div>
                                    @error('clinic_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Doctor Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="doctor_name" class="form-label">Doctor Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                                        <input wire:model="doctor_name" type="text" class="form-control @error('doctor_name') is-invalid @enderror" 
                                               id="doctor_name" placeholder="Dr. John Doe" required minlength="3" maxlength="255" pattern="[a-zA-Z\s\.]+">
                                        <div class="invalid-feedback" id="doctor_name_error" style="display: none;">
                                            Doctor name is required and must be at least 3 characters (letters only).
                                        </div>
                                    </div>
                                    @error('doctor_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" placeholder="doctor@clinic.com" required>
                                        <div class="invalid-feedback" id="email_error" style="display: none;">
                                            Please enter a valid email address.
                                        </div>
                                    </div>
                                    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input wire:model="phone" type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" placeholder="+1 234 567 8900" required minlength="10" maxlength="20" pattern="[+]?[0-9\s\-\(\)]+">
                                        <div class="invalid-feedback" id="phone_error" style="display: none;">
                                            Please enter a valid phone number (10-20 digits).
                                        </div>
                                    </div>
                                    @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <textarea wire:model="address" class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" rows="3" placeholder="Full clinic address" required minlength="10" maxlength="500"></textarea>
                                        <div class="invalid-feedback" id="address_error" style="display: none;">
                                            Address is required and must be at least 10 characters.
                                        </div>
                                    </div>
                                    @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" placeholder="Minimum 6 characters" required minlength="6" maxlength="50">
                                        <div class="invalid-feedback" id="password_error" style="display: none;">
                                            Password must be at least 6 characters long.
                                        </div>
                                    </div>
                                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Password Confirmation -->
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input wire:model="password_confirmation" type="password" class="form-control" 
                                               id="password_confirmation" placeholder="Repeat password" required minlength="6" maxlength="50">
                                        <div class="invalid-feedback" id="password_confirmation_error" style="display: none;">
                                            Password confirmation must match the password.
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Upload -->
                                <div class="col-12 mb-3">
                    <label for="documents" class="form-label">Document <span class="text-muted">(Optional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-file-upload"></i></span>
                        <input wire:model="documents" type="file" class="form-control @error('documents') is-invalid @enderror" 
                               id="documents" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @if($documents)
                            <button type="button" class="btn btn-outline-danger" onclick="clearDocuments()">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        @endif                                                        </div>
                    @error('documents') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    
                    <!-- File Information Display -->
                                    <div id="fileInfoContainer" class="mt-2" style="display: none;">
                                        <div id="fileList"></div>
                                    </div>
                                    
                                    <!-- Error Display -->
                                    <div id="documentErrorContainer" class="mt-2" style="display: none;">
                                        <div class="alert alert-danger alert-sm mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <span id="documentErrorMessage"></span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearDocuments()">
                                                <i class="fas fa-trash me-1"></i>Remove File
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-text">
                                        <small><i class="fas fa-info-circle me-1"></i>Accepted formats: PDF, DOC, DOCX, JPG, PNG. Max size: 5MB per file.</small>
                                    </div>
                                    @error('documents.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-secondary me-md-2" onclick="clearForm()">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" id="submitBtn" disabled>
                                            <span wire:loading.remove wire:target="register">
                                                <i class="fas fa-user-plus me-1"></i>Register Doctor
                                            </span>
                                            <span wire:loading wire:target="register">
                                                <i class="fas fa-spinner fa-spin me-1"></i>Registering...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global function for clearing documents
function clearDocuments() {
    const documentsInput = document.getElementById('documents');
    documentsInput.value = '';
    
    // Clear errors and file info
    const errorContainer = document.getElementById('documentErrorContainer');
    const fileInfoContainer = document.getElementById('fileInfoContainer');
    
    if (errorContainer) errorContainer.style.display = 'none';
    if (fileInfoContainer) fileInfoContainer.style.display = 'none';
    
    // Dispatch a change event to trigger validation update
    documentsInput.dispatchEvent(new Event('change'));
}

// Global function for clearing entire form
function clearForm() {
    const form = document.getElementById('doctorRegistrationForm');
    if (form) {
        // Clear all form inputs
        form.reset();
        
        // Clear all validation classes and errors
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
        
        // Clear all error messages
        const errorDivs = form.querySelectorAll('.invalid-feedback');
        errorDivs.forEach(div => {
            div.style.display = 'none';
        });
        
        // Clear document-specific errors and file info
        const errorContainer = document.getElementById('documentErrorContainer');
        const fileInfoContainer = document.getElementById('fileInfoContainer');
        
        if (errorContainer) errorContainer.style.display = 'none';
        if (fileInfoContainer) fileInfoContainer.style.display = 'none';
        
        // Reset touched fields tracking
        if (typeof touchedFields !== 'undefined') {
            touchedFields.clear();
        }
        
        // Reset submit button to disabled state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        
        console.log('Form cleared successfully');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('doctorRegistrationForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Track which fields have been touched
    const touchedFields = new Set();
    
    // Form validation function
    function validateForm() {
        let isRequiredFieldsValid = true;
        
        // Clinic Name validation
        const clinicName = document.getElementById('clinic_name');
        if (touchedFields.has('clinic_name') && clinicName.value.trim().length < 3) {
            showError('clinic_name', 'Clinic name must be at least 3 characters long.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('clinic_name')) {
            clearError('clinic_name');
        }
        
        // Doctor Name validation
        const doctorName = document.getElementById('doctor_name');
        const namePattern = /^[a-zA-Z\s\.]+$/;
        if (touchedFields.has('doctor_name') && (doctorName.value.trim().length < 3 || !namePattern.test(doctorName.value))) {
            showError('doctor_name', 'Doctor name must be at least 3 characters and contain only letters.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('doctor_name')) {
            clearError('doctor_name');
        }
        
        // Email validation
        const email = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (touchedFields.has('email') && !emailPattern.test(email.value)) {
            showError('email', 'Please enter a valid email address.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('email')) {
            clearError('email');
        }
        
        // Phone validation
        const phone = document.getElementById('phone');
        const phonePattern = /^[+]?[0-9\s\-\(\)]{10,20}$/;
        if (touchedFields.has('phone') && !phonePattern.test(phone.value.replace(/\s/g, ''))) {
            showError('phone', 'Please enter a valid phone number (10-20 digits).');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('phone')) {
            clearError('phone');
        }
        
        // Address validation
        const address = document.getElementById('address');
        if (touchedFields.has('address') && address.value.trim().length < 10) {
            showError('address', 'Address must be at least 10 characters long.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('address')) {
            clearError('address');
        }
        
        // Password validation
        const password = document.getElementById('password');
        if (touchedFields.has('password') && password.value.length < 6) {
            showError('password', 'Password must be at least 6 characters long.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('password')) {
            clearError('password');
        }
        
        // Password confirmation validation
        const passwordConfirmation = document.getElementById('password_confirmation');
        if (touchedFields.has('password_confirmation') && password.value !== passwordConfirmation.value) {
            showError('password_confirmation', 'Password confirmation must match the password.');
            isRequiredFieldsValid = false;
        } else if (touchedFields.has('password_confirmation')) {
            clearError('password_confirmation');
        }
        
        // File validation is handled separately in validateDocuments()
        // This ensures file validation doesn't interfere with required field validation
        
        // Update submit button state based only on required fields
        updateSubmitButtonState();
        
        return isRequiredFieldsValid;
    }
    
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '_error');
        
        field.classList.add('is-invalid');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '_error');
        
        field.classList.remove('is-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }
    
    function showDocumentError(message) {
        const errorContainer = document.getElementById('documentErrorContainer');
        const errorMessage = document.getElementById('documentErrorMessage');
        
        errorMessage.textContent = message;
        errorContainer.style.display = 'block';
    }
    
    function clearDocumentError() {
        const errorContainer = document.getElementById('documentErrorContainer');
        errorContainer.style.display = 'none';
    }
    
    function displayFileInfo(validFiles, invalidFiles) {
        const fileInfoContainer = document.getElementById('fileInfoContainer');
        const fileList = document.getElementById('fileList');
        
        let html = '';
        
        // Display valid files
        validFiles.forEach(file => {
            html += `
                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded mb-1">
                    <div class="d-flex align-items-center text-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <span>${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                    </div>
                </div>
            `;
        });
        
        // Display invalid files
        invalidFiles.forEach(file => {
            html += `
                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded mb-1">
                    <div class="d-flex align-items-center text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>${file.name} - ${file.error}</span>
                    </div>
                </div>
            `;
        });
        
        if (html) {
            fileList.innerHTML = html;
            fileInfoContainer.style.display = 'block';
        } else {
            hideFileInfo();
        }
    }
    
    function hideFileInfo() {
        const fileInfoContainer = document.getElementById('fileInfoContainer');
        fileInfoContainer.style.display = 'none';
    }
    
    // Override the global function with local context
    window.clearDocuments = function() {
        const documentsInput = document.getElementById('documents');
        documentsInput.value = '';
        
        // Clear errors and file info
        clearDocumentError();
        hideFileInfo();
        
        // Clear touched state for documents
        touchedFields.delete('documents');
        
        // Update submit button state to ensure it's properly enabled
        updateSubmitButtonState();
        console.log('Documents cleared, submit button updated');
    }
    
    function validateDocuments() {
        const documents = document.getElementById('documents');
        if (documents.files.length > 0) {
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            let fileError = false;
            let errorMessage = '';
            let validFiles = [];
            let invalidFiles = [];
            
            // Since we only allow one file, just check the first file
            const file = documents.files[0];
            if (!allowedTypes.includes(file.type)) {
                errorMessage = 'Invalid file type: ' + file.name + '. Please select valid file types (PDF, DOC, DOCX, JPG, PNG).';
                invalidFiles.push({name: file.name, error: 'Invalid file type'});
                fileError = true;
            } else if (file.size > maxSize) {
                errorMessage = 'File too large: ' + file.name + '. File must be less than 5MB.';
                invalidFiles.push({name: file.name, error: 'File too large (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)'});
                fileError = true;
            } else {
                validFiles.push(file);
            }
            
            if (fileError) {
                showDocumentError(errorMessage);
                displayFileInfo(validFiles, invalidFiles);
            } else {
                clearDocumentError();
                displayFileInfo(validFiles, []);
            }
        } else {
            clearDocumentError();
            hideFileInfo();
        }
        
        // Call updateSubmitButtonState to ensure submit button is properly enabled
        updateSubmitButtonState();
        console.log('Document validation completed, submit button updated');
    }

    // Mark field as touched and validate
    function handleFieldInteraction(fieldId) {
        touchedFields.add(fieldId);
        validateForm(); // This already calls updateSubmitButtonState()
    }
    
    function updateSubmitButtonState() {
        const requiredFields = ['clinic_name', 'doctor_name', 'email', 'phone', 'address', 'password', 'password_confirmation'];
        
        // Check if all required fields are filled
        const allRequiredFilled = requiredFields.every(fieldId => {
            const field = document.getElementById(fieldId);
            return field && field.value.trim().length > 0;
        });
        
        // Check if any required fields have validation errors (only for touched fields)
        let hasRequiredFieldErrors = false;
        requiredFields.forEach(fieldId => {
            if (touchedFields.has(fieldId)) {
                const field = document.getElementById(fieldId);
                if (field && field.classList.contains('is-invalid')) {
                    hasRequiredFieldErrors = true;
                }
            }
        });
        
        // Enable submit button based only on required fields
        const submitBtn = document.getElementById('submitBtn');
        const shouldEnable = allRequiredFilled && !hasRequiredFieldErrors;
        submitBtn.disabled = !shouldEnable;
        
        // Debug logging
        console.log('Submit button state:', {
            allRequiredFilled,
            hasRequiredFieldErrors,
            shouldEnable,
            disabled: submitBtn.disabled,
            touchedFields: Array.from(touchedFields),
            fieldsWithErrors: requiredFields.filter(fieldId => {
                const field = document.getElementById(fieldId);
                return field && field.classList.contains('is-invalid');
            })
        });
    }
    
    // Real-time validation with touch tracking
    const fields = ['clinic_name', 'doctor_name', 'email', 'phone', 'address', 'password', 'password_confirmation'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', () => handleFieldInteraction(fieldId));
            field.addEventListener('blur', () => handleFieldInteraction(fieldId));
            field.addEventListener('focus', () => {
                // Only mark as touched on focus if field has value
                if (field.value.trim().length > 0) {
                    touchedFields.add(fieldId);
                }
            });
        }
    });
    
    // File input validation with enhanced feedback
    const documentsInput = document.getElementById('documents');
    if (documentsInput) {
        documentsInput.addEventListener('change', () => {
            console.log('File upload triggered, files count:', documentsInput.files.length);
            
            // Only add documents to touched fields and validate files separately
            touchedFields.add('documents');
            
            // Validate files separately without affecting required field validation
            validateDocuments();
            
            console.log('After file upload processing, submit button disabled:', document.getElementById('submitBtn').disabled);
        });
    }
    
    // Form submit validation
    form.addEventListener('submit', function(e) {
        // Mark all required fields as touched on submit attempt (excluding documents)
        const requiredFields = ['clinic_name', 'doctor_name', 'email', 'phone', 'address', 'password', 'password_confirmation'];
        requiredFields.forEach(fieldId => touchedFields.add(fieldId));
        
        // Validate only required fields for form submission
        let formValid = true;
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field || field.value.trim().length === 0) {
                formValid = false;
            }
        });
        
        if (!formValid) {
            e.preventDefault();
            e.stopPropagation();
            validateForm(); // Show validation errors
        }
        // Allow form submission even if document validation fails
    });
    
    // Initial check for submit button state without showing errors
    setTimeout(() => {
        updateSubmitButtonState();
    }, 100);
});
</script>
