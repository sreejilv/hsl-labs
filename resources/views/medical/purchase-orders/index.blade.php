@extends('layouts.medical')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">My Purchase Orders</h1>
                <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>New Order
                </a>
            </div>

            <!-- Status Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('medical.purchase-orders.index') }}" 
                                   class="btn btn-outline-primary {{ !request('status') ? 'active' : '' }}">
                                    All Orders
                                </a>
                                <a href="{{ route('medical.purchase-orders.index', ['status' => 'pending']) }}" 
                                   class="btn btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">
                                    Pending
                                </a>
                                <a href="{{ route('medical.purchase-orders.index', ['status' => 'confirmed']) }}" 
                                   class="btn btn-outline-info {{ request('status') == 'confirmed' ? 'active' : '' }}">
                                    Confirmed
                                </a>
                                <a href="{{ route('medical.purchase-orders.index', ['status' => 'delivered']) }}" 
                                   class="btn btn-outline-success {{ request('status') == 'delivered' ? 'active' : '' }}">
                                    Delivered
                                </a>
                                <a href="{{ route('medical.purchase-orders.index', ['status' => 'cancelled']) }}" 
                                   class="btn btn-outline-danger {{ request('status') == 'cancelled' ? 'active' : '' }}">
                                    Cancelled
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Confirmed By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <strong>{{ $order->order_number }}</strong>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            <td>{{ $order->items->count() }} item(s)</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                @if($order->confirmedBy)
                                                    {{ $order->confirmedBy->first_name }} {{ $order->confirmedBy->last_name }}
                                                    <br>
                                                    <small class="text-muted">{{ $order->confirmed_at->format('M d, Y H:i') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('medical.purchase-orders.show', $order) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4>No purchase orders found</h4>
                            <p class="text-muted">You haven't created any purchase orders yet.</p>
                            <a href="{{ route('medical.purchase-orders.products') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Your First Order
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection