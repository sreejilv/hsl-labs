@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-eye me-2"></i>Product Details</h1>
                <div>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-1"></i>Edit Product
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Product Information -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Product Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Product Code:</strong></td>
                                            <td><code>{{ $product->code }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Product Name:</strong></td>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td><strong class="text-success">${{ number_format($product->price, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Stock Quantity:</strong></td>
                                            <td>
                                                @if($product->stock > 10)
                                                    <span class="badge bg-success fs-6">{{ $product->stock }} units</span>
                                                @elseif($product->stock > 0)
                                                    <span class="badge bg-warning fs-6">{{ $product->stock }} units</span>
                                                @else
                                                    <span class="badge bg-danger fs-6">Out of Stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success fs-6">Active</span>
                                                @else
                                                    <span class="badge bg-secondary fs-6">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created Date:</strong></td>
                                            <td>{{ $product->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated:</strong></td>
                                            <td>{{ $product->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Value:</strong></td>
                                            <td><strong class="text-info">${{ number_format($product->price * $product->stock, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($product->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <hr>
                                    <h6><strong>Description:</strong></h6>
                                    <p class="text-muted">{{ $product->description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Actions & Quick Stats -->
                <div class="col-md-4">
                    <!-- Quick Actions -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Edit Product
                                </a>
                                
                                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                                        <i class="fas fa-{{ $product->is_active ? 'ban' : 'check' }} me-2"></i>
                                        {{ $product->is_active ? 'Deactivate' : 'Activate' }} Product
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Delete Product
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Statistics -->
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Product Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1">${{ number_format($product->price, 2) }}</h4>
                                            <small class="text-muted">Unit Price</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-info mb-1">{{ $product->stock }}</h4>
                                        <small class="text-muted">In Stock</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="text-success mb-1">${{ number_format($product->price * $product->stock, 2) }}</h5>
                                        <small class="text-muted">Total Inventory Value</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            @if($product->images && count($product->images) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-images me-2"></i>Product Images</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($product->image_urls as $index => $imageUrl)
                                    <div class="col-md-3 col-sm-6 mb-3">
                                        <div class="card">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" 
                                                 class="card-img-top" style="height: 200px; object-fit: cover;">
                                            <div class="card-body text-center">
                                                <small class="text-muted">Image {{ $index + 1 }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Images Available</h5>
                            <p class="text-muted">This product doesn't have any images yet.</p>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Images
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the product <strong>{{ $product->name }}</strong>?</p>
                <p class="text-danger"><small>This action cannot be undone and will delete all associated data including images.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection