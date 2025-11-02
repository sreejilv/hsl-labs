@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Patient Order</h1>
                <a href="{{ route('medical.sales-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medical.sales-orders.store') }}" method="POST" id="salesOrderForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Patient Selection -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="patient_id" class="form-label">Patient *</label>
                                            <select name="patient_id" id="patient_id" class="form-select" required>
                                                <option value="">Select Patient</option>
                                                @foreach($patients as $patient)
                                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                                        {{ $patient->full_name }} ({{ $patient->patient_id }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Order Notes</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional notes about this order">{{ old('notes') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Products</h5>
                            </div>
                            <div class="card-body">
                                <div id="productsList">
                                    <!-- Products will be added here dynamically -->
                                </div>
                                
                                <div id="noProducts" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-3"></i>
                                    <p>No products added yet. Click on a product from the list on the right to add it.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Order Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Items:</span>
                                        <span id="totalItems">0</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Quantity:</span>
                                        <span id="totalQuantity">0</span>
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Amount:</strong>
                                        <strong id="totalAmount">$0.00</strong>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                                    <i class="fas fa-save me-1"></i>Create Order
                                </button>
                            </div>
                        </div>

                        <!-- Available Products -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Available Products</h6>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                @foreach($products as $product)
                                    <div class="mb-2 p-2 border rounded">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $product->code }}</small>
                                                <br>
                                                <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                                    {{ $product->stock }} in stock
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <strong>${{ number_format($product->getEffectiveSellingPrice(), 2) }}</strong>
                                                @if($product->selling_price && $product->selling_price != $product->price)
                                                    <br>
                                                    <small class="text-muted">Purchase: ${{ number_format($product->price, 2) }}</small>
                                                @endif
                                                <br>
                                                <button type="button" class="btn btn-sm btn-outline-primary select-product" 
                                                        data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-price="{{ $product->getEffectiveSellingPrice() }}"
                                                        data-stock="{{ $product->stock }}"
                                                        {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Row Template -->
<template id="productRowTemplate">
    <div class="product-row border rounded p-3 mb-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <strong class="product-name"></strong>
                <br>
                <small class="text-muted product-price"></small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control quantity-input" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Total</label>
                <div class="form-control-plaintext total-price">$0.00</div>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <input type="hidden" class="product-id">
        <input type="hidden" class="quantity-hidden">
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productIndex = 0;
    const productsList = document.getElementById('productsList');
    const noProducts = document.getElementById('noProducts');
    const submitBtn = document.getElementById('submitBtn');

    // Add product functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.select-product')) {
            const btn = e.target.closest('.select-product');
            const productId = btn.dataset.id;
            const productName = btn.dataset.name;
            const productPrice = parseFloat(btn.dataset.price);
            const productStock = parseInt(btn.dataset.stock);

            addProductRow(productId, productName, productPrice, productStock);
            btn.disabled = true;
        }

        if (e.target.closest('.remove-product')) {
            const row = e.target.closest('.product-row');
            const productId = row.querySelector('.product-id').value;
            
            // Re-enable the product button
            document.querySelector(`[data-id="${productId}"]`).disabled = false;
            
            row.remove();
            
            // Reindex remaining form fields
            reindexFormFields();
            
            updateSummary();
            toggleSubmitButton();
        }
    });

    // Quantity change handler
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const row = e.target.closest('.product-row');
            const quantity = parseInt(e.target.value) || 0;
            const stock = parseInt(row.dataset.stock);
            
            // Validate quantity
            if (quantity > stock) {
                e.target.value = stock;
                alert(`Maximum available quantity is ${stock}`);
            }
            
            if (quantity < 1) {
                e.target.value = 1;
            }
            
            // Update hidden field
            row.querySelector('.quantity-hidden').value = e.target.value;
            
            updateRowTotal(row);
            updateSummary();
        }
    });

    function addProductRow(productId, productName, productPrice, productStock) {
        const template = document.getElementById('productRowTemplate');
        const clone = template.content.cloneNode(true);
        
        const row = clone.querySelector('.product-row');
        row.dataset.price = productPrice;
        row.dataset.stock = productStock;
        
        clone.querySelector('.product-name').textContent = productName;
        clone.querySelector('.product-price').textContent = `$${productPrice.toFixed(2)} each`;
        
        // Set proper array indices for form fields
        const currentIndex = productIndex++;
        clone.querySelector('.product-id').name = `products[${currentIndex}][product_id]`;
        clone.querySelector('.product-id').value = productId;
        clone.querySelector('.quantity-hidden').name = `products[${currentIndex}][quantity]`;
        clone.querySelector('.quantity-input').max = productStock;
        clone.querySelector('.quantity-input').value = 1; // Set default quantity
        clone.querySelector('.quantity-hidden').value = 1; // Set default quantity
        
        clone.querySelector('.quantity-input').addEventListener('input', function() {
            const hiddenField = row.querySelector('.quantity-hidden');
            hiddenField.value = this.value;
            updateRowTotal(row);
            updateSummary();
        });
        
        productsList.appendChild(clone);
        noProducts.style.display = 'none';
        
        // Update the row total immediately
        updateRowTotal(row);
        updateSummary();
        toggleSubmitButton();
    }

    function reindexFormFields() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            row.querySelector('.product-id').name = `products[${index}][product_id]`;
            row.querySelector('.quantity-hidden').name = `products[${index}][quantity]`;
        });
        productIndex = rows.length; // Reset the product index
    }

    function updateRowTotal(row) {
        const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.dataset.price);
        const total = quantity * price;
        
        row.querySelector('.total-price').textContent = `$${total.toFixed(2)}`;
        row.querySelector('.quantity-hidden').value = quantity;
    }

    function updateSummary() {
        const rows = document.querySelectorAll('.product-row');
        let totalItems = rows.length;
        let totalQuantity = 0;
        let totalAmount = 0;

        rows.forEach(row => {
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.dataset.price);
            
            totalQuantity += quantity;
            totalAmount += quantity * price;
            
            updateRowTotal(row);
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('totalAmount').textContent = `$${totalAmount.toFixed(2)}`;

        if (totalItems === 0) {
            noProducts.style.display = 'block';
        }
    }

    function toggleSubmitButton() {
        const hasProducts = document.querySelectorAll('.product-row').length > 0;
        const hasPatient = document.getElementById('patient_id').value !== '';
        
        submitBtn.disabled = !(hasProducts && hasPatient);
    }

    // Patient selection change
    document.getElementById('patient_id').addEventListener('change', toggleSubmitButton);

    // Form submission validation
    document.getElementById('salesOrderForm').addEventListener('submit', function(e) {
        const products = document.querySelectorAll('.product-row');
        const patientId = document.getElementById('patient_id').value;
        
        console.log('Form submission - Patient ID:', patientId);
        console.log('Form submission - Products count:', products.length);
        
        // Log all form data for debugging
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log('Form data:', key, '=', value);
        }
        
        if (!patientId) {
            e.preventDefault();
            alert('Please select a patient.');
            return false;
        }
        
        if (products.length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the order.');
            return false;
        }
        
        // Validate each product has quantity
        let hasError = false;
        products.forEach((row, index) => {
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const productId = row.querySelector('.product-id').value;
            console.log(`Product ${index}: ID=${productId}, Quantity=${quantity}`);
            
            if (quantity < 1) {
                hasError = true;
                alert(`Product ${index + 1} must have a quantity of at least 1.`);
            }
        });
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
        
        console.log('Form validation passed, submitting...');
        return true;
    });
});
</script>
@endsection