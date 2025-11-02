<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\RecurringOrder;
use App\Models\RecurringOrderItem;
use App\Models\Product;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecurringOrderController extends Controller
{
    /**
     * Display a listing of recurring orders.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get recurring orders based on user role
        $query = RecurringOrder::with(['patient', 'doctor', 'staff', 'items.product']);
        
        if ($user->hasRole('staff')) {
            // Staff can only see their own recurring orders
            $query->where('staff_id', $user->id);
        } elseif ($user->hasRole('surgeon')) {
            // Doctors can see all recurring orders for their patients
            $query->where('doctor_id', $user->id);
        }
        
        $recurringOrders = $query->orderBy('next_due_date', 'asc')->paginate(20);
        
        // Get due orders count for notification
        $dueOrdersCount = $query->where('status', 'active')
                               ->where('next_due_date', '<=', now()->toDateString())
                               ->count();
        
        return view('medical.recurring-orders.index', compact('recurringOrders', 'dueOrdersCount'));
    }

    /**
     * Show the form for creating a new recurring order.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get patients and products based on role
        if ($user->hasRole('staff')) {
            // Check if staff has a valid doctor relationship
            if (!$user->staffDetail || !$user->staffDetail->doctor_id) {
                return redirect()->route('medical.recurring-orders.index')
                    ->with('error', 'Your account is not properly linked to a doctor. Please contact your administrator.');
            }
            
            $doctorId = $user->staffDetail->doctor_id;
            $patients = Patient::where('doctor_id', $doctorId)->active()->get();
            $products = Product::where('stock', '>', 0)->active()->get();
        } else {
            $patients = Patient::where('doctor_id', $user->id)->active()->get();
            $products = Product::where('stock', '>', 0)->active()->get();
        }
        
        return view('medical.recurring-orders.create', compact('patients', 'products'));
    }

    /**
     * Store a newly created recurring order.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'duration_months' => 'required|integer|min:2|max:12',
            'start_date' => 'required|date|after_or_equal:today',
            'day_of_month' => 'required|integer|min:1|max:28',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Determine doctor ID
            if ($user->hasRole('staff')) {
                // Check if staff has a valid doctor relationship
                if (!$user->staffDetail || !$user->staffDetail->doctor_id) {
                    throw new \Exception('Your account is not properly linked to a doctor. Please contact your administrator.');
                }
                $doctorId = $user->staffDetail->doctor_id;
            } else {
                $doctorId = $user->id;
            }

            // Calculate next due date
            $startDate = Carbon::parse($validated['start_date']);
            $dayOfMonth = (int) $validated['day_of_month'];
            $nextDueDate = $startDate->copy()->day($dayOfMonth);
            
            // If the due day has passed in the start month, move to next month
            if ($nextDueDate < $startDate) {
                $nextDueDate->addMonth();
            }

            // Create recurring order
            $recurringOrder = RecurringOrder::create([
                'doctor_id' => $doctorId,
                'patient_id' => $validated['patient_id'],
                'staff_id' => $user->id,
                'frequency' => 'monthly',
                'duration_months' => $validated['duration_months'],
                'remaining_months' => $validated['duration_months'],
                'start_date' => $validated['start_date'],
                'next_due_date' => $nextDueDate->toDateString(),
                'day_of_month' => $validated['day_of_month'],
                'notes' => $validated['notes']
            ]);

            $totalAmount = 0;

            // Create recurring order items
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Use current selling price
                $unitPrice = $product->getEffectiveSellingPrice();
                $totalPrice = $unitPrice * $productData['quantity'];
                $totalAmount += $totalPrice;

                RecurringOrderItem::create([
                    'recurring_order_id' => $recurringOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ]);
            }

            // Update total amount
            $recurringOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('medical.recurring-orders.index')
                ->with('success', 'Recurring order created successfully! It will be processed on the ' . $validated['day_of_month'] . getOrdinalSuffix($validated['day_of_month']) . ' of each month.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating recurring order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified recurring order.
     */
    public function show(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        $recurringOrder->load(['patient', 'doctor', 'staff', 'items.product', 'salesOrders.items']);

        return view('medical.recurring-orders.show', compact('recurringOrder', 'relatedSalesOrders'));
    }

    /**
     * Show the form for editing the specified recurring order.
     */
    public function edit(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        // Don't allow editing completed orders
        if ($recurringOrder->status === 'completed') {
            return redirect()->route('medical.recurring-orders.show', $recurringOrder)
                           ->with('error', 'Cannot edit a completed recurring order.');
        }

        // Get patients and products based on user role
        if ($user->hasRole('staff') && $user->staffDetail && $user->staffDetail->doctor_id) {
            $doctorId = $user->staffDetail->doctor_id;
            $patients = Patient::where('doctor_id', $doctorId)->active()->get();
            $products = Product::where('stock', '>', 0)->active()->get();
        } else {
            $patients = Patient::where('doctor_id', $user->id)->active()->get();
            $products = Product::where('stock', '>', 0)->active()->get();
        }
        
        return view('medical.recurring-orders.edit', compact('recurringOrder', 'patients', 'products'));
    }

    /**
     * Update the specified recurring order.
     */
    public function update(Request $request, RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        // Don't allow updating completed orders
        if ($recurringOrder->status === 'completed') {
            return redirect()->route('medical.recurring-orders.show', $recurringOrder)
                           ->with('error', 'Cannot update a completed recurring order.');
        }
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'duration_months' => 'required|integer|min:2|max:12',
            'start_date' => 'required|date',
            'day_of_month' => 'required|integer|min:1|max:28',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calculate new total amount
            $totalAmount = 0;
            foreach ($validated['products'] as $productData) {
                $totalAmount += $productData['quantity'] * $productData['unit_price'];
            }
            
            // Update the recurring order
            $recurringOrder->update([
                'patient_id' => $validated['patient_id'],
                'duration_months' => $validated['duration_months'],
                'remaining_months' => $validated['duration_months'], // Reset remaining months if duration changed
                'start_date' => $validated['start_date'],
                'day_of_month' => $validated['day_of_month'],
                'notes' => $validated['notes'],
                'total_amount' => $totalAmount,
            ]);
            
            // Delete existing items
            $recurringOrder->items()->delete();
            
            // Add new items
            foreach ($validated['products'] as $productData) {
                $recurringOrder->items()->create([
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total_price' => $productData['quantity'] * $productData['unit_price'],
                ]);
            }
            
            // Recalculate next due date
            $recurringOrder->calculateNextDueDate();
            
            DB::commit();
            
            return redirect()->route('medical.recurring-orders.show', $recurringOrder)
                           ->with('success', 'Recurring order updated successfully.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Failed to update recurring order: ' . $e->getMessage());
        }
    }

    /**
     * Show due recurring orders for processing.
     */
    public function due()
    {
        $user = Auth::user();
        
        $query = RecurringOrder::with(['patient', 'doctor', 'staff', 'items.product'])
                              ->due();
        
        if ($user->hasRole('staff')) {
            $query->where('staff_id', $user->id);
        } elseif ($user->hasRole('surgeon')) {
            $query->where('doctor_id', $user->id);
        }
        
        $dueOrders = $query->orderBy('next_due_date', 'asc')->get();
        
        return view('medical.recurring-orders.due', compact('dueOrders'));
    }

    /**
     * Process a due recurring order.
     */
    public function process(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        try {
            DB::beginTransaction();
            
            $salesOrder = $recurringOrder->processRecurringOrder();
            
            DB::commit();

            return redirect()->route('medical.recurring-orders.due')
                ->with('success', 'Recurring order processed successfully! Sales order ' . $salesOrder->order_number . ' has been created.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error processing recurring order: ' . $e->getMessage());
        }
    }

    /**
     * Pause/Resume a recurring order.
     */
    public function toggleStatus(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        $newStatus = $recurringOrder->status === 'active' ? 'paused' : 'active';
        $recurringOrder->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Recurring order resumed successfully.' : 'Recurring order paused successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Pause a recurring order.
     */
    public function pause(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        if ($recurringOrder->status !== 'active') {
            return back()->with('error', 'Only active orders can be paused.');
        }

        $recurringOrder->update(['status' => 'paused']);

        return back()->with('success', 'Recurring order paused successfully.');
    }

    /**
     * Resume a recurring order.
     */
    public function resume(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        if ($recurringOrder->status !== 'paused') {
            return back()->with('error', 'Only paused orders can be resumed.');
        }

        $recurringOrder->update(['status' => 'active']);

        return back()->with('success', 'Recurring order resumed successfully.');
    }

    /**
     * Cancel a recurring order.
     */
    public function cancel(RecurringOrder $recurringOrder)
    {
        $user = Auth::user();
        
        // Check access permissions
        if ($user->hasRole('staff') && $recurringOrder->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        } elseif ($user->hasRole('surgeon') && $recurringOrder->doctor_id !== $user->id) {
            abort(403, 'Unauthorized access to recurring order.');
        }

        $recurringOrder->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Recurring order cancelled successfully.');
    }
}
