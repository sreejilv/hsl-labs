<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'images',
        'stock',
        'quantity', // Add quantity for available inventory
        'price',
        'selling_price',
        'is_active'
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'selling_price' => 'decimal:2'
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();
        
        // Set selling_price to price by default when creating a new product
        static::creating(function ($product) {
            if (is_null($product->selling_price) && !is_null($product->price)) {
                $product->selling_price = $product->price;
            }
        });
    }

    /**
     * Scope to get only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only inactive products
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Get the first image URL
     */
    public function getFirstImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return asset('storage/' . $this->images[0]);
        }
        return asset('images/no-image.png'); // Default image
    }

    /**
     * Get all image URLs
     */
    public function getImageUrlsAttribute()
    {
        if ($this->images) {
            return array_map(function($image) {
                return asset('storage/' . $image);
            }, $this->images);
        }
        return [];
    }

    // Relationships
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    // Inventory methods
    public function isInStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }

    public function getAvailableQuantity()
    {
        return $this->stock ?? 0;
    }

    public function decrementQuantity($amount)
    {
        if ($this->stock >= $amount) {
            $this->decrement('stock', $amount);
            return true;
        }
        return false;
    }

    public function incrementQuantity($amount)
    {
        $this->increment('stock', $amount);
    }

    /**
     * Get the effective selling price (selling_price if set, otherwise price)
     */
    public function getEffectiveSellingPrice()
    {
        return $this->selling_price ?? $this->price;
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }
}
