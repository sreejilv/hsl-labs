<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DoctorDetail;

class AccountController extends Controller
{
    /**
     * Show the wallet/financial overview page
     */
    public function wallet()
    {
        $user = Auth::user();
        
        // Sample wallet data with dummy financial information
        $walletData = [
            'balance' => 2500.75,
            'currency' => 'USD',
            'pending_amount' => 150.00,
            'total_earned' => 45000.00,
            'total_spent' => 12500.25,
            'credit_limit' => 5000.00,
            'monthly_spending' => 850.50,
        ];
        
        // Recent transactions (dummy data)
        $recentTransactions = [
            [
                'id' => 'TXN001',
                'type' => 'credit',
                'amount' => 500.00,
                'description' => 'Payment received from City Hospital',
                'date' => now()->subDays(1),
                'status' => 'completed'
            ],
            [
                'id' => 'TXN002',
                'type' => 'debit',
                'amount' => 75.00,
                'description' => 'Medical supplies purchase',
                'date' => now()->subDays(2),
                'status' => 'completed'
            ],
            [
                'id' => 'TXN003',
                'type' => 'credit',
                'amount' => 1200.00,
                'description' => 'Surgery fee - Patient #12345',
                'date' => now()->subDays(3),
                'status' => 'pending'
            ],
            [
                'id' => 'TXN004',
                'type' => 'debit',
                'amount' => 250.00,
                'description' => 'Equipment maintenance',
                'date' => now()->subDays(5),
                'status' => 'completed'
            ],
        ];
        
        return view('admin.account.wallet', compact('user', 'walletData', 'recentTransactions'));
    }

    /**
     * Show the transactions page
     */
    public function transactions()
    {
        $user = Auth::user();
        
        // Sample transaction history with more detailed dummy data
        $transactions = [
            [
                'id' => 'TXN001',
                'type' => 'credit',
                'amount' => 500.00,
                'description' => 'Payment received from City Hospital',
                'reference' => 'INV-2025-001',
                'date' => now()->subDays(1),
                'status' => 'completed',
                'category' => 'Patient Payment'
            ],
            [
                'id' => 'TXN002',
                'type' => 'debit',
                'amount' => 75.00,
                'description' => 'Medical supplies purchase',
                'reference' => 'PO-2025-045',
                'date' => now()->subDays(2),
                'status' => 'completed',
                'category' => 'Supplies'
            ],
            [
                'id' => 'TXN003',
                'type' => 'credit',
                'amount' => 1200.00,
                'description' => 'Surgery fee - Patient #12345',
                'reference' => 'SRG-2025-023',
                'date' => now()->subDays(3),
                'status' => 'pending',
                'category' => 'Surgery Fee'
            ],
            [
                'id' => 'TXN004',
                'type' => 'debit',
                'amount' => 250.00,
                'description' => 'Equipment maintenance',
                'reference' => 'MAINT-2025-012',
                'date' => now()->subDays(5),
                'status' => 'completed',
                'category' => 'Maintenance'
            ],
            [
                'id' => 'TXN005',
                'type' => 'credit',
                'amount' => 800.00,
                'description' => 'Consultation fees - Week 43',
                'reference' => 'CONS-2025-W43',
                'date' => now()->subWeek(),
                'status' => 'completed',
                'category' => 'Consultation'
            ],
            [
                'id' => 'TXN006',
                'type' => 'debit',
                'amount' => 120.00,
                'description' => 'Software subscription renewal',
                'reference' => 'SUB-2025-SOFT',
                'date' => now()->subWeeks(2),
                'status' => 'completed',
                'category' => 'Software'
            ],
        ];
        
        return view('admin.account.transactions', compact('user', 'transactions'));
    }

    /**
     * Show the orders page
     */
    public function orders()
    {
        $user = Auth::user();
        
        // Sample orders with dummy data
        $orders = [
            [
                'id' => 'ORD-2025-045',
                'vendor' => 'Medical Supplies Inc.',
                'amount' => 1250.00,
                'items_count' => 15,
                'order_date' => now()->subDays(2),
                'delivery_date' => now()->addDays(3),
                'status' => 'pending',
                'description' => 'Surgical instruments and disposables'
            ],
            [
                'id' => 'ORD-2025-044',
                'vendor' => 'HealthTech Equipment',
                'amount' => 3500.00,
                'items_count' => 3,
                'order_date' => now()->subDays(7),
                'delivery_date' => now()->subDays(2),
                'status' => 'delivered',
                'description' => 'Digital blood pressure monitors'
            ],
            [
                'id' => 'ORD-2025-043',
                'vendor' => 'PharmaCorp',
                'amount' => 850.00,
                'items_count' => 25,
                'order_date' => now()->subDays(10),
                'delivery_date' => now()->subDays(8),
                'status' => 'completed',
                'description' => 'Emergency medications stock'
            ],
            [
                'id' => 'ORD-2025-042',
                'vendor' => 'Office Supplies Pro',
                'amount' => 180.00,
                'items_count' => 8,
                'order_date' => now()->subDays(15),
                'delivery_date' => now()->subDays(12),
                'status' => 'completed',
                'description' => 'Administrative supplies'
            ],
        ];
        
        return view('admin.account.orders', compact('user', 'orders'));
    }

    /**
     * Show financial reports page
     */
    public function reports()
    {
        $user = Auth::user();
        
        // Sample financial reports data
        $reportsData = [
            'monthly_revenue' => 8500.00,
            'monthly_expenses' => 2300.00,
            'monthly_profit' => 6200.00,
            'year_to_date_revenue' => 92000.00,
            'year_to_date_expenses' => 28000.00,
            'top_revenue_sources' => [
                ['source' => 'Surgery Fees', 'amount' => 45000.00, 'percentage' => 49],
                ['source' => 'Consultations', 'amount' => 28000.00, 'percentage' => 30],
                ['source' => 'Procedures', 'amount' => 19000.00, 'percentage' => 21],
            ],
            'monthly_trends' => [
                ['month' => 'Jan', 'revenue' => 7800, 'expenses' => 2100],
                ['month' => 'Feb', 'revenue' => 8200, 'expenses' => 2300],
                ['month' => 'Mar', 'revenue' => 8900, 'expenses' => 2800],
                ['month' => 'Apr', 'revenue' => 8100, 'expenses' => 2200],
                ['month' => 'May', 'revenue' => 9200, 'expenses' => 2600],
                ['month' => 'Jun', 'revenue' => 8700, 'expenses' => 2400],
            ]
        ];
        
        return view('admin.account.reports', compact('user', 'reportsData'));
    }

    /**
     * Add funds to wallet
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'payment_method' => 'required|string',
        ]);

        // In a real application, you would process the payment here
        // For now, we'll just redirect with success message
        
        return redirect()->route('admin.account.wallet')
                        ->with('success', 'Funds added successfully! Amount: $' . number_format($request->amount, 2));
    }
}