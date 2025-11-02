<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of all purchase orders.
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['doctor', 'items.product', 'confirmedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                      $doctorQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.purchase-orders.index', compact('orders'));
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $order)
    {
        $order->load(['doctor', 'items.product', 'confirmedBy']);

        return view('admin.purchase-orders.show', compact('order'));
    }

    /**
     * Confirm a purchase order.
     */
    public function confirm(PurchaseOrder $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending orders can be confirmed.');
        }

        // Check if all products have sufficient quantity
        foreach ($order->items as $item) {
            if (!$item->product->isInStock($item->quantity)) {
                return redirect()->back()
                    ->with('error', "Insufficient stock for {$item->product->name}. Available: {$item->product->stock}, Requested: {$item->quantity}");
            }
        }

        // Confirm the order (this will also decrement product quantities)
        $order->confirm(Auth::id());

        return redirect()->back()
            ->with('success', 'Purchase order confirmed successfully!');
    }

    /**
     * Cancel a purchase order.
     */
    public function cancel(PurchaseOrder $order, Request $request)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => $order->notes . "\n\nCancelled by admin: " . ($request->reason ?? 'No reason provided')
        ]);

        return redirect()->back()
            ->with('success', 'Purchase order cancelled successfully!');
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(PurchaseOrder $order)
    {
        if ($order->status !== 'confirmed') {
            return redirect()->back()
                ->with('error', 'Only confirmed orders can be marked as delivered.');
        }

        $order->markAsDelivered();

        return redirect()->back()
            ->with('success', 'Order marked as delivered successfully!');
    }

    /**
     * Show order history and statistics.
     */
    public function history(Request $request)
    {
        $query = PurchaseOrder::with(['doctor', 'items.product']);

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $stats = [
            'total_orders' => PurchaseOrder::count(),
            'pending_orders' => PurchaseOrder::where('status', 'pending')->count(),
            'confirmed_orders' => PurchaseOrder::where('status', 'confirmed')->count(),
            'delivered_orders' => PurchaseOrder::where('status', 'delivered')->count(),
            'cancelled_orders' => PurchaseOrder::where('status', 'cancelled')->count(),
            'total_revenue' => PurchaseOrder::whereIn('status', ['confirmed', 'delivered'])->sum('total_amount')
        ];

        return view('admin.purchase-orders.history', compact('orders', 'stats'));
    }

    /**
     * Show inventory status.
     */
    public function inventory()
    {
        $products = Product::with(['purchaseOrderItems' => function ($query) {
            $query->whereHas('purchaseOrder', function ($q) {
                $q->where('status', 'confirmed');
            });
        }])
        ->paginate(20);

        return view('admin.purchase-orders.inventory', compact('products'));
    }
}
