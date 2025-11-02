@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Recurring Order #{{ $recurringOrder->id }}</h4>
                    <div>
                        <a href="{{ route('medical.recurring-orders.show', $recurringOrder) }}" class="btn btn-info btn-sm me-2">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('medical.recurring-orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($recurringOrder->status === 'completed')
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> This recurring order has been completed and cannot be modified.
                        </div>
                    @endif

                    <form action="{{ route('medical.recurring-orders.update', $recurringOrder) }}" method="POST" id="recurringOrderForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                                    <select name="patient_id" id="patient_id" class="form-control" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                        <option value="">Select Patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id', $recurringOrder->patient_id) == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->name }} ({{ $patient->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="duration_months" class="form-label">Duration <span class="text-danger">*</span></label>
                                    <select name="duration_months" id="duration_months" class="form-control" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                        <option value="">Select Duration</option>
                                        @for($i = 2; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('duration_months', $recurringOrder->duration_months) == $i ? 'selected' : '' }}>
                                                {{ $i }} Month{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           value="{{ old('start_date', $recurringOrder->start_date->format('Y-m-d')) }}" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="day_of_month" class="form-label">Monthly Processing Day <span class="text-danger">*</span></label>
                                    <select name="day_of_month" id="day_of_month" class="form-control" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                        <option value="">Select Day</option>
                                        @for($i = 1; $i <= 28; $i++)
                                            <option value="{{ $i }}" {{ old('day_of_month', $recurringOrder->day_of_month) == $i ? 'selected' : '' }}>
                                                {{ $i }}{{ getOrdinalSuffix($i) }} of each month
                                            </option>
                                        @endfor
                                    </select>
                                    <small class="form-text text-muted">Orders will be automatically processed on this day each month</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" 
                                      placeholder="Optional notes about this recurring order" {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>{{ old('notes', $recurringOrder->notes) }}</textarea>
                        </div>

                        <hr>

                        <h5 class="mb-3">Products</h5>
                        <div id="products-section">
                            @foreach($recurringOrder->items as $index => $item)
                                <div class="product-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <input type="hidden" name="products[{{ $index }}][id]" value="{{ $item->id }}">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                                <select name="products[{{ $index }}][product_id]" class="form-control product-select" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                                data-price="{{ $product->getEffectiveSellingPrice() }}"
                                                                data-stock="{{ $product->stock }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }} (Stock: {{ $product->stock }}) - ${{ number_format($product->getEffectiveSellingPrice(), 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                                <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity-input" 
                                                       min="1" value="{{ old('products.'.$index.'.quantity', $item->quantity) }}" required {{ $recurringOrder->status === 'completed' ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Unit Price</label>
                                                <input type="number" name="products[{{ $index }}][unit_price]" class="form-control unit-price-input" 
                                                       step="0.01" value="{{ old('products.'.$index.'.unit_price', $item->unit_price) }}" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Total</label>
                                                <input type="number" class="form-control total-price-input" step="0.01" value="{{ $item->total_price }}" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                @if($recurringOrder->status !== 'completed')
                                                    <button type="button" class="btn btn-danger btn-sm remove-product d-block" {{ count($recurringOrder->items) <= 1 ? 'style=display:none;' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($recurringOrder->status !== 'completed')
                            <div class="mb-3">
                                <button type="button" id="add-product" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Order Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Total Amount:</span>
                                            <span id="grand-total" class="fw-bold">${{ number_format($recurringOrder->total_amount, 2) }}</span>
                                        </div>
                                        <small class="text-muted">This amount will be charged monthly</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('medical.recurring-orders.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            @if($recurringOrder->status !== 'completed')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Recurring Order
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if($recurringOrder->status !== 'completed')
@push('scripts')
<script>
$(document).ready(function() {
    let productIndex = {{ count($recurringOrder->items) }};

    // Add product functionality
    $('#add-product').click(function() {
        const productItem = $('.product-item:first').clone();
        productItem.attr('data-index', productIndex);
        
        // Clear the hidden ID field and update form field names
        productItem.find('input[type="hidden"]').remove();
        productItem.find('select').attr('name', `products[${productIndex}][product_id]`).val('');
        productItem.find('.quantity-input').attr('name', `products[${productIndex}][quantity]`).val('1');
        productItem.find('.unit-price-input').attr('name', `products[${productIndex}][unit_price]`).val('');
        productItem.find('.total-price-input').val('');
        
        // Show remove button
        productItem.find('.remove-product').show();
        
        $('#products-section').append(productItem);
        productIndex++;
        
        updateRemoveButtons();
    });

    // Remove product functionality
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-item').remove();
        updateRemoveButtons();
        calculateGrandTotal();
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const productItems = $('.product-item');
        if (productItems.length > 1) {
            $('.remove-product').show();
        } else {
            $('.remove-product').hide();
        }
    }

    // Product selection change
    $(document).on('change', '.product-select', function() {
        const option = $(this).find('option:selected');
        const price = option.data('price') || 0;
        const stock = option.data('stock') || 0;
        const productItem = $(this).closest('.product-item');
        
        productItem.find('.unit-price-input').val(price);
        productItem.find('.quantity-input').attr('max', stock);
        
        calculateItemTotal(productItem);
    });

    // Quantity change
    $(document).on('input', '.quantity-input', function() {
        const productItem = $(this).closest('.product-item');
        calculateItemTotal(productItem);
    });

    // Calculate item total
    function calculateItemTotal(productItem) {
        const quantity = parseFloat(productItem.find('.quantity-input').val()) || 0;
        const unitPrice = parseFloat(productItem.find('.unit-price-input').val()) || 0;
        const total = quantity * unitPrice;
        
        productItem.find('.total-price-input').val(total.toFixed(2));
        calculateGrandTotal();
    }

    // Calculate grand total
    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.total-price-input').each(function() {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#grand-total').text('$' + grandTotal.toFixed(2));
    }

    // Initial calculation
    calculateGrandTotal();

    // Form validation
    $('#recurringOrderForm').submit(function(e) {
        let valid = true;
        let hasProducts = false;

        $('.product-item').each(function() {
            const productId = $(this).find('.product-select').val();
            const quantity = $(this).find('.quantity-input').val();
            
            if (productId && quantity) {
                hasProducts = true;
            }
        });

        if (!hasProducts) {
            e.preventDefault();
            alert('Please add at least one product to the recurring order.');
            valid = false;
        }

        return valid;
    });
});
</script>
@endpush
@endif
@endsection