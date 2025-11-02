@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-bell text-warning me-2"></i>Due Recurring Orders
                </h1>
                <a href="{{ route('medical.recurring-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to All Orders
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($dueOrders->count() > 0)
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ $dueOrders->count() }}</strong> recurring order(s) are due for processing. 
                    Process each order to create the monthly patient order and update inventory.
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">Orders Due for Processing</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Patient</th>
                                        <th>Due Date</th>
                                        <th>Products</th>
                                        <th>Monthly Amount</th>
                                        <th>Remaining Months</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dueOrders as $order)
                                        <tr class="table-warning">
                                            <td>
                                                <a href="{{ route('medical.recurring-orders.show', $order) }}" class="text-decoration-none">
                                                    <strong>{{ $order->recurring_order_number }}</strong>
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->patient->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $order->patient->patient_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-danger fw-bold">{{ $order->next_due_date->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $order->next_due_date->diffForHumans() }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @foreach($order->items as $item)
                                                        <div class="mb-1">
                                                            <strong>{{ $item->product->name }}</strong> × {{ $item->quantity }}
                                                            <br>
                                                            <span class="text-muted">${{ number_format($item->total_price, 2) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $order->remaining_months }} months</span>
                                            </td>
                                            <td>
                                                <div class="d-grid gap-2">
                                                    <form action="{{ route('medical.recurring-orders.process', $order) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm w-100"
                                                                onclick="return confirm('Process this recurring order now? This will:\n\n1. Create a new sales order\n2. Decrease product inventory\n3. Update the next due date\n\nContinue?')">
                                                            <i class="fas fa-play me-1"></i>Process Now
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('medical.recurring-orders.show', $order) }}" 
                                                       class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>No Orders Due</h4>
                        <p class="text-muted">All recurring orders are up to date. No processing required at this time.</p>
                        <a href="{{ route('medical.recurring-orders.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i>View All Recurring Orders
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection