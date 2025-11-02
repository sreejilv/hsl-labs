<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders for the authenticated doctor.
     */
    public function index()
    {
        $orders = PurchaseOrder::with(['items.product', 'confirmedBy'])
            ->where('doctor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('medical.purchase-orders.index', compact('orders'));
    }

    /**
     * Show products available for purchase.
     */
    public function products(Request $request)
    {
        $query = Product::active()->inStock();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(12);

        return view('medical.purchase-orders.products', compact('products'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(Request $request)
    {
        // Get selected products from session or request
        $selectedProducts = [];
        if ($request->has('products')) {
            $productIds = is_array($request->products) ? $request->products : [$request->products];
            $selectedProducts = Product::whereIn('id', $productIds)->get();
        }

        return view('medical.purchase-orders.create', compact('selectedProducts'));
    }

    /**
     * Store a newly created purchase order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($request) {
            // Create purchase order
            $order = PurchaseOrder::create([
                'doctor_id' => Auth::id(),
                'status' => 'pending',
                'notes' => $request->notes,
                'total_amount' => 0
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['id']);
                
                // Check if enough quantity is available
                if (!$product->isInStock($productData['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $productData['quantity'];
                $totalAmount += $totalPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);
            }

            // Update total amount
            $order->update(['total_amount' => $totalAmount]);
        });

        return redirect()->route('medical.purchase-orders.index')
            ->with('success', 'Purchase order created successfully! Waiting for admin confirmation.');
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $order)
    {
        // Ensure the order belongs to the authenticated doctor
        if ($order->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access to purchase order.');
        }

        $order->load(['items.product', 'confirmedBy']);

        return view('medical.purchase-orders.show', compact('order'));
    }

    /**
     * Get delivered orders (products in hand).
     */
    public function inventory()
    {
        $deliveredOrders = PurchaseOrder::with(['items.product'])
            ->where('doctor_id', Auth::id())
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->paginate(10);

        // Calculate total quantities in hand by product
        $productsInHand = PurchaseOrderItem::whereHas('purchaseOrder', function ($query) {
            $query->where('doctor_id', Auth::id())
                  ->where('status', 'delivered');
        })
        ->with('product')
        ->selectRaw('product_id, SUM(quantity) as total_quantity')
        ->groupBy('product_id')
        ->get();

        return view('medical.purchase-orders.inventory', compact('deliveredOrders', 'productsInHand'));
    }
}
