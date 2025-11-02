@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Purchase Order Details</h1>
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">
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
                                    <p><strong>Doctor:</strong> {{ $order->doctor->first_name }} {{ $order->doctor->last_name }}</p>
                                    <p><strong>Email:</strong> {{ $order->doctor->email }}</p>
                                    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
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
                                    @if($order->confirmedBy)
                                        <p><strong>Confirmed By:</strong> {{ $order->confirmedBy->first_name }} {{ $order->confirmedBy->last_name }}</p>
                                        <p><strong>Confirmed At:</strong> {{ $order->confirmed_at->format('M d, Y H:i') }}</p>
                                    @endif
                                    @if($order->delivered_at)
                                        <p><strong>Delivered At:</strong> {{ $order->delivered_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($order->notes)
                                <div class="mt-3">
                                    <strong>Notes:</strong>
                                    <p class="mt-2">{{ $order->notes }}</p>
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
                                            <th>Available Stock</th>
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
                                                    </div>
                                                </td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $item->product->quantity >= $item->quantity ? 'success' : 'danger' }}">
                                                        {{ $item->product->quantity }} available
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total Amount:</th>
                                            <th>${{ number_format($order->total_amount, 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($order->status === 'pending')
                                <!-- Check Stock Availability -->
                                @php
                                    $hasStockIssues = false;
                                    foreach($order->items as $item) {
                                        if($item->product->quantity < $item->quantity) {
                                            $hasStockIssues = true;
                                            break;
                                        }
                                    }
                                @endphp

                                @if($hasStockIssues)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Some items have insufficient stock. Please check inventory before confirming.
                                    </div>
                                @endif

                                <form action="{{ route('admin.purchase-orders.confirm', $order) }}" method="POST" class="mb-3">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100" 
                                            {{ $hasStockIssues ? 'disabled' : '' }}
                                            onclick="return confirm('Are you sure you want to confirm this order? This will deduct the items from inventory.')">
                                        <i class="fas fa-check me-1"></i>Confirm Order
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger w-100" 
                                        data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-1"></i>Cancel Order
                                </button>

                                <!-- Cancel Modal -->
                                <div class="modal fade" id="cancelModal" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.purchase-orders.cancel', $order) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cancel Order</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to cancel this order?</p>
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason for cancellation</label>
                                                        <textarea class="form-control" name="reason" id="reason" 
                                                                  rows="3" placeholder="Enter reason for cancellation..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif($order->status === 'confirmed')
                                <form action="{{ route('admin.purchase-orders.deliver', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info w-100"
                                            onclick="return confirm('Mark this order as delivered?')">
                                        <i class="fas fa-truck me-1"></i>Mark as Delivered
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This order has been {{ $order->status }}.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6>Order Created</h6>
                                        <p class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>

                                @if($order->confirmed_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6>Order Confirmed</h6>
                                            <p class="text-muted">{{ $order->confirmed_at->format('M d, Y H:i') }}</p>
                                            <small>by {{ $order->confirmedBy->first_name }} {{ $order->confirmedBy->last_name }}</small>
                                        </div>
                                    </div>
                                @endif

                                @if($order->delivered_at)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-info"></div>
                                        <div class="timeline-content">
                                            <h6>Order Delivered</h6>
                                            <p class="text-muted">{{ $order->delivered_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
    color: #495057;
}

.timeline-content p {
    margin-bottom: 5px;
    font-size: 0.875rem;
}
</style>
@endsection