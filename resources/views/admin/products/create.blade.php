@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-plus me-2"></i>Add New Product</h1>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Product Form -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-box me-2"></i>Product Information</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="productForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Product Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Unique identifier for the product</small>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional product description</small>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="images" class="form-label">Product Images</label>
                                    <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                           id="images" name="images[]" multiple accept="image/*">
                                    @error('images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Select multiple images (JPEG, PNG, JPG, GIF). Max 2MB per image.
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Product</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Uncheck to create as inactive product</small>
                                </div>
                            </div>
                        </div>

                        <!-- Image Preview Area -->
                        <div class="row">
                            <div class="col-12">
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <label class="form-label">Selected Images Preview:</label>
                                    <div id="previewContainer" class="d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Product
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

<script>
// Form validation object
const ProductFormValidator = {
    init() {
        this.form = document.getElementById('productForm');
        this.setupEventListeners();
        this.setupImagePreview();
    },

    setupEventListeners() {
        // Real-time validation on input
        const fields = ['code', 'name', 'price', 'stock'];
        fields.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('blur', () => this.validateField(field));
                input.addEventListener('input', () => this.clearErrors(field));
            }
        });

        // Form submission validation
        this.form.addEventListener('submit', (e) => this.validateForm(e));
    },

    validateField(fieldName) {
        const field = document.getElementById(fieldName);
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        switch (fieldName) {
            case 'code':
                if (!value) {
                    errorMessage = 'Product code is required';
                    isValid = false;
                } else if (value.length < 3) {
                    errorMessage = 'Product code must be at least 3 characters';
                    isValid = false;
                } else if (!/^[A-Z0-9]+$/.test(value)) {
                    errorMessage = 'Product code should contain only uppercase letters and numbers';
                    isValid = false;
                }
                break;

            case 'name':
                if (!value) {
                    errorMessage = 'Product name is required';
                    isValid = false;
                } else if (value.length < 2) {
                    errorMessage = 'Product name must be at least 2 characters';
                    isValid = false;
                } else if (value.length > 255) {
                    errorMessage = 'Product name cannot exceed 255 characters';
                    isValid = false;
                }
                break;

            case 'price':
                if (!value) {
                    errorMessage = 'Price is required';
                    isValid = false;
                } else if (isNaN(value) || parseFloat(value) < 0) {
                    errorMessage = 'Price must be a valid positive number';
                    isValid = false;
                } else if (parseFloat(value) > 999999.99) {
                    errorMessage = 'Price cannot exceed $999,999.99';
                    isValid = false;
                }
                break;

            case 'stock':
                if (!value && value !== '0') {
                    errorMessage = 'Stock quantity is required';
                    isValid = false;
                } else if (isNaN(value) || parseInt(value) < 0) {
                    errorMessage = 'Stock must be a valid non-negative number';
                    isValid = false;
                } else if (parseInt(value) > 999999) {
                    errorMessage = 'Stock cannot exceed 999,999 units';
                    isValid = false;
                }
                break;
        }

        this.showFieldError(fieldName, errorMessage, !isValid);
        return isValid;
    },

    validateImages() {
        const imageInput = document.getElementById('images');
        const files = imageInput.files;
        let isValid = true;
        let errorMessage = '';

        if (files.length > 5) {
            errorMessage = 'Maximum 5 images allowed';
            isValid = false;
        } else {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file type
                if (!file.type.startsWith('image/')) {
                    errorMessage = 'Only image files are allowed';
                    isValid = false;
                    break;
                }
                
                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    errorMessage = `Image "${file.name}" exceeds 2MB limit`;
                    isValid = false;
                    break;
                }
            }
        }

        this.showFieldError('images', errorMessage, !isValid);
        return isValid;
    },

    validateForm(e) {
        let isValid = true;
        
        // Validate all required fields
        const fields = ['code', 'name', 'price', 'stock'];
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Validate images
        if (!this.validateImages()) {
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            this.showFormError('Please correct the errors above before submitting');
            this.scrollToFirstError();
        } else {
            this.clearFormError();
        }
    },

    showFieldError(fieldName, message, hasError) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.getElementById(`${fieldName}-error`) || this.createErrorDiv(fieldName);
        
        if (hasError) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            errorDiv.style.display = 'none';
        }
    },

    clearErrors(fieldName) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.getElementById(`${fieldName}-error`);
        
        field.classList.remove('is-invalid', 'is-valid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    },

    createErrorDiv(fieldName) {
        const field = document.getElementById(fieldName);
        const errorDiv = document.createElement('div');
        errorDiv.id = `${fieldName}-error`;
        errorDiv.className = 'invalid-feedback';
        errorDiv.style.display = 'none';
        field.parentNode.appendChild(errorDiv);
        return errorDiv;
    },

    showFormError(message) {
        let errorAlert = document.getElementById('form-error-alert');
        if (!errorAlert) {
            errorAlert = document.createElement('div');
            errorAlert.id = 'form-error-alert';
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.innerHTML = `
                <strong>Validation Error:</strong> <span id="form-error-message"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            this.form.insertBefore(errorAlert, this.form.firstChild);
        }
        document.getElementById('form-error-message').textContent = message;
        errorAlert.style.display = 'block';
    },

    clearFormError() {
        const errorAlert = document.getElementById('form-error-alert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    },

    scrollToFirstError() {
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    },

    setupImagePreview() {
        document.getElementById('images').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            
            previewContainer.innerHTML = '';
            
            if (files.length > 0) {
                imagePreview.style.display = 'block';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imageWrapper = document.createElement('div');
                            imageWrapper.className = 'position-relative';
                            imageWrapper.innerHTML = `
                                <img src="${e.target.result}" class="img-thumbnail" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <small class="d-block text-center text-muted mt-1">
                                    ${(file.size / 1024).toFixed(1)}KB
                                </small>
                            `;
                            previewContainer.appendChild(imageWrapper);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                imagePreview.style.display = 'none';
            }
            
            // Validate images on change
            ProductFormValidator.validateImages();
        });
    }
};

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    ProductFormValidator.init();
    
    // Auto-uppercase product code
    const codeField = document.getElementById('code');
    if (codeField) {
        codeField.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Format price input
    const priceField = document.getElementById('price');
    if (priceField) {
        priceField.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
});
</script>
@endsection