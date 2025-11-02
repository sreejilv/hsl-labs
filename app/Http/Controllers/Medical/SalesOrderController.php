<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Patient;
use App\Models\Product;
use App\Models\StaffDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderController extends Controller
{
    /**
     * Get the doctor ID for the current staff member
     */
    private function getDoctorId()
    {
        $user = Auth::user();
        
        if ($user->hasRole('staff')) {
            $staffDetail = StaffDetail::where('user_id', $user->id)->first();
            return $staffDetail ? $staffDetail->doctor_id : null;
        }
        
        return null;
    }

    /**
     * Display a listing of sales orders.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('staff')) {
            // Staff sees orders they created
            $salesOrders = SalesOrder::with(['patient', 'doctor', 'items.product'])
                ->where('staff_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Doctors see orders for their inventory
            $salesOrders = SalesOrder::with(['patient', 'staff', 'items.product'])
                ->where('doctor_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('medical.sales-orders.index', compact('salesOrders'));
    }

    /**
     * Show the form for creating a new sales order.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only staff can create sales orders
        if (!$user->hasRole('staff')) {
            return redirect()->route('medical.sales-orders.index')
                ->with('error', 'Only staff members can create sales orders.');
        }

        $doctorId = $this->getDoctorId();
        
        if (!$doctorId) {
            abort(403, 'No doctor assigned to this staff member.');
        }

        // Get patients for this doctor
        $patients = Patient::where('doctor_id', $doctorId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Get products with stock available
        $products = Product::active()
            ->inStock()
            ->orderBy('name')
            ->get();

        return view('medical.sales-orders.create', compact('patients', 'products'));
    }

    /**
     * Store a newly created sales order.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only staff can create sales orders
        if (!$user->hasRole('staff')) {
            return redirect()->route('medical.sales-orders.index')
                ->with('error', 'Only staff members can create sales orders.');
        }

        $doctorId = $this->getDoctorId();
        
        if (!$doctorId) {
            abort(403, 'No doctor assigned to this staff member.');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Create sales order
            $salesOrder = SalesOrder::create([
                'doctor_id' => $doctorId,
                'patient_id' => $validated['patient_id'],
                'staff_id' => $user->id,
                'status' => 'completed', // Instantly confirm staff orders
                'completed_at' => now(),
                'notes' => $validated['notes'] ?? null
            ]);

            $totalAmount = 0;

            // Create sales order items and update inventory immediately
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Check stock availability
                if (!$product->isInStock($productData['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Use selling price instead of purchase price
                $unitPrice = $product->getEffectiveSellingPrice();
                $totalPrice = $unitPrice * $productData['quantity'];
                $totalAmount += $totalPrice;

                SalesOrderItem::create([
                    'sales_order_id' => $salesOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);

                // Immediately decrease stock since order is auto-confirmed
                if (!$product->decrementQuantity($productData['quantity'])) {
                    throw new \Exception("Failed to update stock for product: {$product->name}");
                }
            }

            // Update total amount
            $salesOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('medical.sales-orders.index')
                ->with('success', 'Patient order has been created and confirmed successfully! Stock has been updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating sales order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sales order.
     */
    public function show(SalesOrder $salesOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $salesOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to sales order.');
        } elseif ($user->hasRole('surgeon') && $salesOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to sales order.');
        }

        $salesOrder->load(['patient', 'doctor', 'staff', 'items.product']);

        return view('medical.sales-orders.show', compact('salesOrder'));
    }
}
