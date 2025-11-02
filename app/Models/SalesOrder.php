<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'doctor_id',
        'patient_id',
        'staff_id',
        'recurring_order_id',
        'total_amount',
        'status',
        'notes',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($salesOrder) {
            if (empty($salesOrder->order_number)) {
                $salesOrder->order_number = 'SO' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
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
        return $this->hasMany(SalesOrderItem::class);
    }

    public function recurringOrder()
    {
        return $this->belongsTo(RecurringOrder::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Business Logic
    public function complete()
    {
        // Decrease stock for all items when order is completed
        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product->decrementQuantity($item->quantity)) {
                throw new \Exception("Insufficient stock for product: {$product->name}");
            }
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function calculateTotal()
    {
        $total = $this->items->sum('total_price');
        $this->update(['total_amount' => $total]);
        return $total;
    }
}