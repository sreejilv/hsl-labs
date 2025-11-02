@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Inventory in Hand</h1>
                <div>
                    <a href="{{ route('medical.purchase-orders.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-list me-1"></i>My Orders
                    </a>
                    <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>New Order
                    </a>
                </div>
            </div>

            <!-- Products in Hand Summary -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Products Available in Hand</h5>
                        </div>
                        <div class="card-body">
                            @if($productsInHand->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Available Stock</th>
                                                <th>Purchase Price</th>
                                                <th>Selling Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($productsInHand as $productSummary)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $productSummary->product->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $productSummary->product->code }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success fs-6">{{ $productSummary->total_quantity }} units</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-primary">${{ number_format($productSummary->product->price, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <div id="selling-price-display-{{ $productSummary->product->id }}">
                                                            <span class="text-success fw-bold">
                                                                ${{ number_format($productSummary->product->getEffectiveSellingPrice(), 2) }}
                                                            </span>
                                                            @if($productSummary->product->selling_price)
                                                                <small class="text-muted">(custom)</small>
                                                            @else
                                                                <small class="text-muted">(default)</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary edit-price-btn" 
                                                                data-product-id="{{ $productSummary->product->id }}"
                                                                data-product-name="{{ $productSummary->product->name }}"
                                                                data-current-price="{{ $productSummary->product->getEffectiveSellingPrice() }}">
                                                            <i class="fas fa-edit"></i> Set Price
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                    <h5>No products in hand</h5>
                                    <p class="text-muted">You don't have any delivered products yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivered Orders History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivered Orders History</h5>
                </div>
                <div class="card-body">
                    @if($deliveredOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Delivered Date</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deliveredOrders as $order)
                                        <tr>
                                            <td>
                                                <strong>{{ $order->order_number }}</strong>
                                            </td>
                                            <td>{{ $order->delivered_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <div class="small">
                                                    @foreach($order->items as $item)
                                                        <div>{{ $item->product->name }} ({{ $item->quantity }})</div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('medical.purchase-orders.show', $order) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $deliveredOrders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h4>No delivered orders yet</h4>
                            <p class="text-muted">Once your orders are confirmed and delivered, they will appear here.</p>
                            <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create New Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Selling Price Modal -->
<div class="modal fade" id="sellingPriceModal" tabindex="-1" aria-labelledby="sellingPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sellingPriceModalLabel">Set Selling Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sellingPriceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="productNameDisplay" class="form-label">Product</label>
                        <input type="text" class="form-control" id="productNameDisplay" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="sellingPrice" class="form-label">Selling Price ($)</label>
                        <input type="number" class="form-control" id="sellingPrice" step="0.01" min="0" required>
                        <div class="form-text">Set the price at which staff will sell this product to patients. Leave blank to use purchase price as default.</div>
                    </div>
                    <input type="hidden" id="productId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="resetPriceBtn">Reset to Purchase Price</button>
                    <button type="submit" class="btn btn-primary">Save Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit price button clicks
    document.querySelectorAll('.edit-price-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const currentPrice = this.dataset.currentPrice;
            
            document.getElementById('productId').value = productId;
            document.getElementById('productNameDisplay').value = productName;
            document.getElementById('sellingPrice').value = currentPrice;
            
            const modal = new bootstrap.Modal(document.getElementById('sellingPriceModal'));
            modal.show();
        });
    });
    
    // Handle reset price button
    document.getElementById('resetPriceBtn').addEventListener('click', function() {
        const productId = document.getElementById('productId').value;
        updateSellingPrice(productId, null);
    });
    
    // Handle form submission
    document.getElementById('sellingPriceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const productId = document.getElementById('productId').value;
        const sellingPrice = document.getElementById('sellingPrice').value;
        
        updateSellingPrice(productId, sellingPrice);
    });
    
    function updateSellingPrice(productId, price) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/medical/products/${productId}/selling-price`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                selling_price: price
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the display
                const displayElement = document.getElementById(`selling-price-display-${productId}`);
                const priceText = price ? `$${parseFloat(price).toFixed(2)}` : `$${data.purchase_price}`;
                const typeText = price ? '(custom)' : '(default)';
                
                displayElement.innerHTML = `
                    <span class="text-success fw-bold">${priceText}</span>
                    <small class="text-muted">${typeText}</small>
                `;
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('sellingPriceModal'));
                modal.hide();
                
                // Show success message
                showAlert('success', 'Selling price updated successfully!');
            } else {
                showAlert('error', data.message || 'Failed to update selling price');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while updating the selling price');
        });
    }
    
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        container.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    }
});
</script>
@endsection