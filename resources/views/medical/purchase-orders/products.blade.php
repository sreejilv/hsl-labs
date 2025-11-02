@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Browse Products</h1>
                <a href="{{ route('medical.purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-list me-1"></i>My Orders
                </a>
            </div>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('medical.purchase-orders.products') }}">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search products by name, code, or description..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <form id="orderForm" action="{{ route('medical.purchase-orders.create') }}" method="GET">
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 product-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock }} in stock
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted mb-2">Code: {{ $product->code }}</p>
                                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0">${{ number_format($product->price, 2) }}</span>
                                        @if($product->stock > 0)
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> Available
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                <i class="fas fa-times-circle"></i> Out of Stock
                                            </span>
                                        @endif
                                    </div>

                                    @if($product->stock > 0)
                                        <div class="form-check">
                                            <input class="form-check-input product-select" type="checkbox" 
                                                   name="products[]" value="{{ $product->id }}" 
                                                   id="product{{ $product->id }}">
                                            <label class="form-check-label" for="product{{ $product->id }}">
                                                Select for order
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <h4>No products found</h4>
                                <p class="text-muted">Try adjusting your search criteria</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>

                <!-- Create Order Button -->
                <div class="fixed-bottom bg-white border-top p-3" id="orderActions" style="display: none;">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="selectedCount">0 products selected</span>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-1"></i>Create Purchase Order
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.product-select');
    const orderActions = document.getElementById('orderActions');
    const selectedCount = document.getElementById('selectedCount');

    function updateOrderActions() {
        const checked = document.querySelectorAll('.product-select:checked').length;
        selectedCount.textContent = checked + ' product' + (checked !== 1 ? 's' : '') + ' selected';
        orderActions.style.display = checked > 0 ? 'block' : 'none';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateOrderActions);
    });
});
</script>
@endsection