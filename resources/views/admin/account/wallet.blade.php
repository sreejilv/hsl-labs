@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-wallet me-2"></i>Wallet & Balance</h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Wallet Balance Card -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Account Balance</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center p-4">
                                        <h2 class="display-4 text-success">${{ number_format($walletData['balance'], 2) }}</h2>
                                        <p class="text-muted">Available Balance</p>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                                            <i class="fas fa-plus me-1"></i>Add Funds
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Pending Amount:</span>
                                                <strong class="text-warning">${{ number_format($walletData['pending_amount'], 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Credit Limit:</span>
                                                <strong class="text-info">${{ number_format($walletData['credit_limit'], 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span>Monthly Spending:</span>
                                                <strong class="text-danger">${{ number_format($walletData['monthly_spending'], 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Financial Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <h4 class="text-success">${{ number_format($walletData['total_earned'], 2) }}</h4>
                                    <small class="text-muted">Total Earned</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <h4 class="text-danger">${{ number_format($walletData['total_spent'], 2) }}</h4>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                                <div class="col-12">
                                    <h4 class="text-primary">${{ number_format($walletData['total_earned'] - $walletData['total_spent'], 2) }}</h4>
                                    <small class="text-muted">Net Profit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recent Transactions</h4>
                                <a href="{{ route('admin.account.transactions') }}" class="btn btn-light btn-sm">
                                    <i class="fas fa-eye me-1"></i>View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td><code>{{ $transaction['id'] }}</code></td>
                                            <td>
                                                @if($transaction['type'] === 'credit')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-down me-1"></i>Credit
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-arrow-up me-1"></i>Debit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction['description'] }}</td>
                                            <td>
                                                <strong class="{{ $transaction['type'] === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction['type'] === 'credit' ? '+' : '-' }}${{ number_format($transaction['amount'], 2) }}
                                                </strong>
                                            </td>
                                            <td>{{ $transaction['date']->format('M d, Y') }}</td>
                                            <td>
                                                @if($transaction['status'] === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
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
    </div>
</div>

<!-- Add Funds Modal -->
<div class="modal fade" id="addFundsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Funds to Wallet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.account.wallet.add-funds') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="1" max="10000" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-credit-card me-1"></i>Add Funds
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection