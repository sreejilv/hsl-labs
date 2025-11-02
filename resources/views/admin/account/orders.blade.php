@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-shopping-cart me-2"></i>Orders</h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ count($orders) }}</h4>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ count(array_filter($orders, function($order) { return $order['status'] === 'completed'; })) }}</h4>
                                    <p class="mb-0">Completed</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ count(array_filter($orders, function($order) { return $order['status'] === 'pending'; })) }}</h4>
                                    <p class="mb-0">Pending</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>${{ number_format(array_sum(array_column($orders, 'amount')), 2) }}</h4>
                                    <p class="mb-0">Total Value</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-list me-2"></i>Orders List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Vendor</th>
                                    <th>Description</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Order Date</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td><code>{{ $order['id'] }}</code></td>
                                    <td>
                                        <strong>{{ $order['vendor'] }}</strong>
                                    </td>
                                    <td>{{ Str::limit($order['description'], 40) }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $order['items_count'] }} items</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($order['amount'], 2) }}</strong>
                                    </td>
                                    <td>{{ $order['order_date']->format('M d, Y') }}</td>
                                    <td>{{ $order['delivery_date']->format('M d, Y') }}</td>
                                    <td>
                                        @if($order['status'] === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($order['status'] === 'delivered')
                                            <span class="badge bg-info">Delivered</span>
                                        @elseif($order['status'] === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($order['status']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                                    data-bs-target="#orderModal{{ $loop->index }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modals -->
@foreach($orders as $index => $order)
<div class="modal fade" id="orderModal{{ $index }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i>Order Details - {{ $order['id'] }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td>{{ $order['id'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Vendor:</strong></td>
                                <td>{{ $order['vendor'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td><strong class="text-success">${{ number_format($order['amount'], 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Items Count:</strong></td>
                                <td><span class="badge bg-info">{{ $order['items_count'] }} items</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order Date:</strong></td>
                                <td>{{ $order['order_date']->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Delivery Date:</strong></td>
                                <td>{{ $order['delivery_date']->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order['status']) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Days Until Delivery:</strong></td>
                                <td>
                                    {{ $order['delivery_date']->diffInDays(now(), false) > 0 ? 'Delivered' : $order['delivery_date']->diffInDays(now()) . ' days' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <h6>Description:</h6>
                        <p>{{ $order['description'] }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success">
                    <i class="fas fa-edit me-1"></i>Edit Order
                </button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Download Order
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection