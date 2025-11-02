<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class RecurringOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_order_number',
        'doctor_id',
        'patient_id',
        'staff_id',
        'frequency',
        'duration_months',
        'remaining_months',
        'start_date',
        'next_due_date',
        'day_of_month',
        'status',
        'total_amount',
        'notes',
        'last_processed_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_due_date' => 'date',
        'last_processed_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($recurringOrder) {
            if (empty($recurringOrder->recurring_order_number)) {
                $recurringOrder->recurring_order_number = 'RO' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items()
    {
        return $this->hasMany(RecurringOrderItem::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'recurring_order_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDue($query)
    {
        return $query->where('status', 'active')
                    ->where('next_due_date', '<=', now()->toDateString());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', 'active')
                    ->where('next_due_date', '>', now()->toDateString())
                    ->where('next_due_date', '<=', now()->addDays($days)->toDateString());
    }

    // Business Logic
    public function isDue()
    {
        return $this->status === 'active' && $this->next_due_date <= now()->toDateString();
    }

    public function calculateNextDueDate()
    {
        if ($this->frequency === 'monthly') {
            $nextMonth = Carbon::parse($this->next_due_date)->addMonth();
            
            // Ensure we use the correct day of month, handling edge cases
            $targetDay = (int) $this->day_of_month;
            $daysInMonth = $nextMonth->daysInMonth;
            
            if ($targetDay > $daysInMonth) {
                $targetDay = $daysInMonth; // Use last day of month if target day doesn't exist
            }
            
            return $nextMonth->day($targetDay)->toDateString();
        }
        
        return $this->next_due_date;
    }

    public function processRecurringOrder()
    {
        if (!$this->isDue()) {
            throw new \Exception('Recurring order is not due for processing');
        }

        if ($this->remaining_months <= 0) {
            throw new \Exception('Recurring order has no remaining months');
        }

        // Create a new sales order
        $salesOrder = SalesOrder::create([
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,
            'staff_id' => $this->staff_id,
            'recurring_order_id' => $this->id,
            'status' => 'completed',
            'completed_at' => now(),
            'total_amount' => $this->total_amount,
            'notes' => "Recurring order: {$this->recurring_order_number}"
        ]);

        // Copy items from recurring order template
        foreach ($this->items as $recurringItem) {
            $product = $recurringItem->product;
            
            // Check stock availability
            if (!$product->isInStock($recurringItem->quantity)) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }

            // Use current selling price (may have changed since recurring order creation)
            $currentSellingPrice = $product->getEffectiveSellingPrice();
            
            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $recurringItem->product_id,
                'quantity' => $recurringItem->quantity,
                'unit_price' => $currentSellingPrice,
                'total_price' => $currentSellingPrice * $recurringItem->quantity
            ]);

            // Decrease stock
            if (!$product->decrementQuantity($recurringItem->quantity)) {
                throw new \Exception("Failed to update stock for product: {$product->name}");
            }
        }

        // Recalculate sales order total based on current prices
        $newTotal = $salesOrder->items->sum('total_price');
        $salesOrder->update(['total_amount' => $newTotal]);

        // Update recurring order
        $this->decrement('remaining_months');
        $this->update([
            'next_due_date' => $this->calculateNextDueDate(),
            'last_processed_at' => now()
        ]);

        // Mark as completed if no months remaining
        if ($this->remaining_months <= 0) {
            $this->update(['status' => 'completed']);
        }

        return $salesOrder;
    }

    public function calculateTotal()
    {
        $total = $this->items->sum('total_price');
        $this->update(['total_amount' => $total]);
        return $total;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-success',
            'paused' => 'bg-warning',
            'completed' => 'bg-secondary',
            'cancelled' => 'bg-danger'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }
}
