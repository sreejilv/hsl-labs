@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recurring Order #{{ $recurringOrder->id }}</h4>
                    <div>
                        @if($recurringOrder->status !== 'completed')
                            <a href="{{ route('medical.recurring-orders.edit', $recurringOrder) }}" class="btn btn-warning btn-sm me-2">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('medical.recurring-orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Patient:</strong></td>
                                    <td>{{ $recurringOrder->patient->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $recurringOrder->patient->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>{{ $recurringOrder->duration_months }} months</td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $recurringOrder->start_date->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>End Date:</strong></td>
                                    <td>{{ $recurringOrder->start_date->addMonths($recurringOrder->duration_months)->subDay()->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Processing Day:</strong></td>
                                    <td>{{ $recurringOrder->day_of_month }}{{ getOrdinalSuffix($recurringOrder->day_of_month) }} of each month</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Status Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $recurringOrder->status === 'active' ? 'success' : ($recurringOrder->status === 'paused' ? 'warning' : ($recurringOrder->status === 'cancelled' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($recurringOrder->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Orders Processed:</strong></td>
                                    <td>{{ $recurringOrder->orders_processed }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Next Due Date:</strong></td>
                                    <td>
                                        @if($recurringOrder->next_due_date && $recurringOrder->status === 'active')
                                            {{ $recurringOrder->next_due_date->format('F j, Y') }}
                                            @if($recurringOrder->next_due_date->isPast())
                                                <span class="badge badge-warning ms-2">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong>${{ number_format($recurringOrder->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $recurringOrder->createdBy->name }} ({{ $recurringOrder->createdBy->email }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($recurringOrder->notes)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Notes</h5>
                                <div class="alert alert-info">
                                    {{ $recurringOrder->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Products</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recurringOrder->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total:</th>
                                            <th>${{ number_format($recurringOrder->total_amount, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($recurringOrder->status !== 'completed' && $recurringOrder->status !== 'cancelled')
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5>Actions</h5>
                                <div class="btn-group" role="group">
                                    @if($recurringOrder->status === 'active')
                                        <form action="{{ route('medical.recurring-orders.pause', $recurringOrder) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to pause this recurring order?')">
                                                <i class="fas fa-pause"></i> Pause
                                            </button>
                                        </form>
                                    @elseif($recurringOrder->status === 'paused')
                                        <form action="{{ route('medical.recurring-orders.resume', $recurringOrder) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to resume this recurring order?')">
                                                <i class="fas fa-play"></i> Resume
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('medical.recurring-orders.cancel', $recurringOrder) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this recurring order? This action cannot be undone.')">
                                            <i class="fas fa-times"></i> Cancel Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Related Sales Orders -->
                    @if($relatedSalesOrders && $relatedSalesOrders->count() > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Generated Sales Orders</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($relatedSalesOrders as $salesOrder)
                                                <tr>
                                                    <td>#{{ $salesOrder->id }}</td>
                                                    <td>{{ $salesOrder->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $salesOrder->status === 'completed' ? 'success' : 'primary' }}">
                                                            {{ ucfirst($salesOrder->status) }}
                                                        </span>
                                                    </td>
                                                    <td>${{ number_format($salesOrder->total_amount, 2) }}</td>
                                                    <td>
                                                        <a href="{{ route('medical.sales-orders.show', $salesOrder) }}" class="btn btn-info btn-xs">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection