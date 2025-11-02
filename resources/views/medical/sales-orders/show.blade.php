@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    @if(auth()->user()->hasRole('staff'))
                        Patient Order Details
                    @else
                        Sales Order Details
                    @endif
                </h1>
                <a href="{{ route('medical.sales-orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Orders
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
                                    <p><strong>Order Number:</strong> {{ $salesOrder->order_number }}</p>
                                    <p><strong>Order Date:</strong> {{ $salesOrder->created_at->format('M d, Y H:i') }}</p>
                                    <p><strong>Status:</strong> 
                                        @switch($salesOrder->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Patient:</strong> {{ $salesOrder->patient->full_name }}</p>
                                    <p><strong>Patient ID:</strong> {{ $salesOrder->patient->patient_id }}</p>
                                    <p><strong>Created By:</strong> {{ $salesOrder->staff->first_name }} {{ $salesOrder->staff->last_name }} (Staff)</p>
                                    @if($salesOrder->completed_at)
                                        <p><strong>Completed At:</strong> {{ $salesOrder->completed_at->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($salesOrder->notes)
                                <div class="mt-3">
                                    <strong>Notes:</strong>
                                    <p class="mt-2 bg-light p-3 rounded">{{ $salesOrder->notes }}</p>
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
                                        @foreach($salesOrder->items as $item)
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
                                            <th>${{ number_format($salesOrder->total_amount, 2) }}</th>
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
                            @switch($salesOrder->status)
                                @case('completed')
                                    <div class="text-success mb-3">
                                        <i class="fas fa-check-circle fa-3x"></i>
                                    </div>
                                    <h6 class="text-success">Order Completed</h6>
                                    <p class="text-muted">Order has been completed and inventory updated.</p>
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

                    <!-- Patient Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Patient Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $salesOrder->patient->full_name }}</p>
                            <p><strong>Patient ID:</strong> {{ $salesOrder->patient->patient_id }}</p>
                            <p><strong>Phone:</strong> {{ $salesOrder->patient->phone }}</p>
                            <p><strong>Email:</strong> {{ $salesOrder->patient->email ?? 'N/A' }}</p>
                            @if($salesOrder->patient->blood_group)
                                <p><strong>Blood Group:</strong> 
                                    <span class="badge bg-danger">{{ $salesOrder->patient->blood_group }}</span>
                                </p>
                            @endif
                            
                            <a href="{{ route('medical.patients.show', $salesOrder->patient) }}" 
                               class="btn btn-outline-info w-100">
                                <i class="fas fa-user me-1"></i>View Patient Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection