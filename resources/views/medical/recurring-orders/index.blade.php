@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Recurring Orders</h1>
                @if(auth()->user()->hasRole('staff'))
                    <a href="{{ route('medical.recurring-orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Setup Recurring Order
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

            @if($dueOrdersCount > 0)
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-bell me-2"></i>
                    <strong>{{ $dueOrdersCount }}</strong> recurring order(s) are due for processing. 
                    <a href="{{ route('medical.recurring-orders.due') }}" class="alert-link">Process them now</a>
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
                                        Total Recurring Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recurringOrders->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
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
                                        Active Orders
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $recurringOrders->where('status', 'active')->count() }}
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
                                        Due for Processing
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dueOrdersCount }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bell fa-2x text-gray-300"></i>
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
                                        Monthly Value
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($recurringOrders->where('status', 'active')->sum('total_amount'), 2) }}
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

            <!-- Recurring Orders Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recurring Orders List</h6>
                </div>
                <div class="card-body">
                    @if($recurringOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Patient</th>
                                        @if(auth()->user()->hasRole('surgeon'))
                                            <th>Created By</th>
                                        @endif
                                        <th>Duration</th>
                                        <th>Next Due</th>
                                        <th>Remaining</th>
                                        <th>Monthly Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recurringOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('medical.recurring-orders.show', $order) }}" class="text-decoration-none">
                                                    {{ $order->recurring_order_number }}
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
                                            <td>
                                                {{ $order->duration_months }} months
                                                <br>
                                                <small class="text-muted">{{ $order->day_of_month }}{{ getOrdinalSuffix($order->day_of_month) }} of each month</small>
                                            </td>
                                            <td>
                                                {{ $order->next_due_date->format('M d, Y') }}
                                                @if($order->isDue())
                                                    <span class="badge bg-danger ms-1">Due Now</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->remaining_months }} months</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('medical.recurring-orders.show', $order) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($order->status === 'active')
                                                        @if($order->isDue())
                                                            <form action="{{ route('medical.recurring-orders.process', $order) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success" 
                                                                        title="Process Order"
                                                                        onclick="return confirm('Process this recurring order now?')">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        
                                                        <form action="{{ route('medical.recurring-orders.toggle-status', $order) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                                    title="Pause Order">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        </form>
                                                    @elseif($order->status === 'paused')
                                                        <form action="{{ route('medical.recurring-orders.toggle-status', $order) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    title="Resume Order">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if(in_array($order->status, ['active', 'paused']))
                                                        <form action="{{ route('medical.recurring-orders.cancel', $order) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                    title="Cancel Order"
                                                                    onclick="return confirm('Are you sure you want to cancel this recurring order?')">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $recurringOrders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-sync-alt fa-3x text-muted mb-3"></i>
                            <h4>No recurring orders found</h4>
                            <p class="text-muted">
                                @if(auth()->user()->hasRole('staff'))
                                    You haven't set up any recurring orders yet.
                                @else
                                    No recurring orders have been created yet.
                                @endif
                            </p>
                            @if(auth()->user()->hasRole('staff'))
                                <a href="{{ route('medical.recurring-orders.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Setup Your First Recurring Order
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection