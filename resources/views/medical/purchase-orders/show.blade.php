@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Order Details</h1>
                <a href="{{ route('medical.purchase-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
                </a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Order Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                                    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Status:</strong> 
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending Confirmation</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-info">Confirmed</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success">Delivered</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if($order->confirmedBy)
                                        <p><strong>Confirmed By:</strong> {{ $order->confirmedBy->first_name }} {{ $order->confirmedBy->last_name }}</p>
                                        <p><strong>Confirmed At:</strong> {{ $order->confirmed_at->format('M d, Y H:i') }}</p>
                                    @endif
                                    @if($order->delivered_at)
                                        <p><strong>Delivered At:</strong> {{ $order->delivered_at->format('M d, Y H:i') }}</p>
                                    @endif
                                    <p><strong>Total Amount:</strong> <span class="text-success h5">${{ number_format($order->total_amount, 2) }}</span></p>
                                </div>
                            </div>
                            @if($order->notes)
                                <div class="mt-3">
                                    <strong>Notes:</strong>
                                    <p class="mt-2 bg-light p-3 rounded">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Unit Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong>{{ $item->product->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $item->product->code }}</small>
                                                        @if($item->product->description)
                                                            <br>
                                                            <small class="text-muted">{{ Str::limit($item->product->description, 80) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="3">Total Amount:</th>
                                            <th>${{ number_format($order->total_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Order Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Status</h5>
                        </div>
                        <div class="card-body text-center">
                            @switch($order->status)
                                @case('pending')
                                    <div class="text-warning mb-3">
                                        <i class="fas fa-clock fa-3x"></i>
                                    </div>
                                    <h6 class="text-warning">Pending Confirmation</h6>
                                    <p class="text-muted">Your order is waiting for admin confirmation.</p>
                                    @break
                                @case('confirmed')
                                    <div class="text-info mb-3">
                                        <i class="fas fa-check-circle fa-3x"></i>
                                    </div>
                                    <h6 class="text-info">Order Confirmed</h6>
                                    <p class="text-muted">Your order has been confirmed and is being prepared for delivery.</p>
                                    @break
                                @case('delivered')
                                    <div class="text-success mb-3">
                                        <i class="fas fa-truck fa-3x"></i>
                                    </div>
                                    <h6 class="text-success">Order Delivered</h6>
                                    <p class="text-muted">Your order has been delivered. Check your inventory!</p>
                                    <a href="{{ route('medical.purchase-orders.inventory') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-boxes me-1"></i>View Inventory
                                    </a>
                                    @break
                                @case('cancelled')
                                    <div class="text-danger mb-3">
                                        <i class="fas fa-times-circle fa-3x"></i>
                                    </div>
                                    <h6 class="text-danger">Order Cancelled</h6>
                                    <p class="text-muted">This order has been cancelled.</p>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item {{ $order->created_at ? 'completed' : '' }}">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6>Order Placed</h6>
                                        <p class="text-muted mb-0">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="timeline-item {{ $order->confirmed_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $order->confirmed_at ? 'bg-success' : 'bg-light' }}"></div>
                                    <div class="timeline-content">
                                        <h6>Order Confirmed</h6>
                                        @if($order->confirmed_at)
                                            <p class="text-muted mb-0">{{ $order->confirmed_at->format('M d, Y H:i') }}</p>
                                            <small>by {{ $order->confirmedBy->first_name }} {{ $order->confirmedBy->last_name }}</small>
                                        @else
                                            <p class="text-muted mb-0">Waiting for confirmation</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="timeline-item {{ $order->delivered_at ? 'completed' : 'pending' }}">
                                    <div class="timeline-marker {{ $order->delivered_at ? 'bg-info' : 'bg-light' }}"></div>
                                    <div class="timeline-content">
                                        <h6>Order Delivered</h6>
                                        @if($order->delivered_at)
                                            <p class="text-muted mb-0">{{ $order->delivered_at->format('M d, Y H:i') }}</p>
                                        @else
                                            <p class="text-muted mb-0">{{ $order->status === 'confirmed' ? 'Being prepared' : 'Pending' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-plus me-1"></i>Create New Order
                            </a>
                            <a href="{{ route('medical.purchase-orders.inventory') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-boxes me-1"></i>View My Inventory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item.completed .timeline-content h6 {
    color: #495057;
}

.timeline-item.pending .timeline-content h6 {
    color: #6c757d;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
}

.timeline-content h6 {
    margin-bottom: 5px;
}

.timeline-content p {
    margin-bottom: 5px;
    font-size: 0.875rem;
}
</style>
@endsection