<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relationships
    public function recurringOrder()
    {
        return $this->belongsTo(RecurringOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
