@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Create Purchase Order</h1>
                <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Products
                </a>
            </div>

            <form action="{{ route('medical.purchase-orders.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Selected Products</h5>
                            </div>
                            <div class="card-body">
                                @if($selectedProducts->isEmpty())
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No products selected. <a href="{{ route('medical.purchase-orders.products') }}">Browse products</a> to get started.</p>
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Available</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productsTable">
                                                @foreach($selectedProducts as $index => $product)
                                                    <tr data-product-id="{{ $product->id }}">
                                                        <td>
                                                            <div>
                                                                <strong>{{ $product->name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $product->code }}</small>
                                                            </div>
                                                            <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
                                                        </td>
                                                        <td class="product-price">${{ number_format($product->price, 2) }}</td>
                                                        <td>{{ $product->stock }}</td>
                                                        <td>
                                                            <input type="number" class="form-control quantity-input" 
                                                                   name="products[{{ $index }}][quantity]" 
                                                                   min="1" max="{{ $product->stock }}" 
                                                                   value="1" required
                                                                   data-price="{{ $product->price }}">
                                                        </td>
                                                        <td class="product-total">${{ number_format($product->price, 2) }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger remove-product">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Any special instructions or notes..."></textarea>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Items:</span>
                                    <span id="itemCount">{{ $selectedProducts->count() }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total Amount:</strong>
                                    <strong id="totalAmount">${{ number_format($selectedProducts->sum('price'), 2) }}</strong>
                                </div>

                                @if($selectedProducts->isNotEmpty())
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-shopping-cart me-1"></i>Create Purchase Order
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateOrderSummary() {
        let total = 0;
        let itemCount = 0;
        
        document.querySelectorAll('#productsTable tr').forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const price = parseFloat(quantityInput.dataset.price);
            const quantity = parseInt(quantityInput.value) || 0;
            const lineTotal = price * quantity;
            
            row.querySelector('.product-total').textContent = '$' + lineTotal.toFixed(2);
            total += lineTotal;
            itemCount++;
        });
        
        document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
        document.getElementById('itemCount').textContent = itemCount;
    }

    // Update totals when quantity changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            updateOrderSummary();
        }
    });

    // Remove product from order
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            e.target.closest('tr').remove();
            updateOrderSummary();
            
            // Update form indices
            document.querySelectorAll('#productsTable tr').forEach((row, index) => {
                row.querySelector('input[name*="[id]"]').name = `products[${index}][id]`;
                row.querySelector('input[name*="[quantity]"]').name = `products[${index}][quantity]`;
            });
        }
    });

    // Initial calculation
    updateOrderSummary();
});
</script>
@endsection