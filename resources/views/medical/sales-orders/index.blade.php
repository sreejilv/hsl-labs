@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    @if(auth()->user()->hasRole('staff'))
                        Patient Orders
                    @else
                        Sales Orders
                    @endif
                </h1>
                @if(auth()->user()->hasRole('staff'))
                    <a href="{{ route('medical.sales-orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create New Order
                    </a>
                @endif
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

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $salesOrders->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Completed Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $salesOrders->where('status', 'completed')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $salesOrders->where('status', 'pending')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Revenue
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($salesOrders->where('status', 'completed')->sum('total_amount'), 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if(auth()->user()->hasRole('staff'))
                            My Patient Orders
                        @else
                            Sales Orders List
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($salesOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Patient</th>
                                        @if(auth()->user()->hasRole('surgeon'))
                                            <th>Created By</th>
                                        @endif
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('medical.sales-orders.show', $order) }}" class="text-decoration-none">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->patient->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $order->patient->patient_id }}</small>
                                                </div>
                                            </td>
                                            @if(auth()->user()->hasRole('surgeon'))
                                                <td>
                                                    {{ $order->staff->first_name }} {{ $order->staff->last_name }}
                                                    <br>
                                                    <small class="text-muted">Staff</small>
                                                </td>
                                            @endif
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            <td>{{ $order->items->count() }} items</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                @switch($order->status)
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
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medical.sales-orders.show', $order) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $salesOrders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h4>No orders found</h4>
                            <p class="text-muted">
                                @if(auth()->user()->hasRole('staff'))
                                    You haven't created any patient orders yet.
                                @else
                                    No sales orders have been created yet.
                                @endif
                            </p>
                            @if(auth()->user()->hasRole('staff'))
                                <a href="{{ route('medical.sales-orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create Your First Order
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection