@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Inventory Status</h1>
                <div>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Orders
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                        <i class="fas fa-box me-1"></i>Manage Products
                    </a>
                </div>
            </div>

            <!-- Inventory Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">In Stock</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('stock', '>', 10)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('stock', '>', 0)->where('stock', '<=', 10)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $products->where('stock', '<=', 0)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Products</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $products->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Inventory Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Product Inventory Details</h5>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th>Price</th>
                                        <th>Available Quantity</th>
                                        <th>Reserved Quantity</th>
                                        <th>Status</th>
                                        <th>Recent Orders</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        @php
                                            $reservedQuantity = $product->purchaseOrderItems->sum('quantity');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $product->code }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>
                                                <span class="h6 {{ $product->stock <= 0 ? 'text-danger' : ($product->stock <= 10 ? 'text-warning' : 'text-success') }}">
                                                    {{ $product->stock }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($reservedQuantity > 0)
                                                    <span class="badge bg-info">{{ $reservedQuantity }} reserved</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->stock <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($product->stock <= 10)
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">In Stock</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->purchaseOrderItems->count() > 0)
                                                    <div class="small">
                                                        @foreach($product->purchaseOrderItems->take(3) as $orderItem)
                                                            <div>
                                                                {{ $orderItem->purchaseOrder->order_number }} 
                                                                ({{ $orderItem->quantity }})
                                                            </div>
                                                        @endforeach
                                                        @if($product->purchaseOrderItems->count() > 3)
                                                            <div class="text-muted">+{{ $product->purchaseOrderItems->count() - 3 }} more</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">No orders</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                            <h4>No products found</h4>
                            <p class="text-muted">No products are available in the inventory.</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add First Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Low Stock Alert -->
            @php
                $lowStockProducts = $products->where('stock', '>', 0)->where('stock', '<=', 10);
                $outOfStockProducts = $products->where('stock', '<=', 0);
            @endphp

            @if($lowStockProducts->count() > 0 || $outOfStockProducts->count() > 0)
                <div class="row mt-4">
                    @if($outOfStockProducts->count() > 0)
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Out of Stock Alert
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-danger">The following products are out of stock:</p>
                                    <ul class="list-unstyled">
                                        @foreach($outOfStockProducts->take(5) as $product)
                                            <li class="mb-1">
                                                <strong>{{ $product->name }}</strong> ({{ $product->code }})
                                            </li>
                                        @endforeach
                                        @if($outOfStockProducts->count() > 5)
                                            <li class="text-muted">+{{ $outOfStockProducts->count() - 5 }} more products</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($lowStockProducts->count() > 0)
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-warning">The following products are running low:</p>
                                    <ul class="list-unstyled">
                                        @foreach($lowStockProducts->take(5) as $product)
                                            <li class="mb-1">
                                                <strong>{{ $product->name }}</strong> ({{ $product->stock }} left)
                                            </li>
                                        @endforeach
                                        @if($lowStockProducts->count() > 5)
                                            <li class="text-muted">+{{ $lowStockProducts->count() - 5 }} more products</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection