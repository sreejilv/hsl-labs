<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Update the selling price for a product (doctors only)
     */
    public function updateSellingPrice(Request $request, Product $product)
    {
        $request->validate([
            'selling_price' => 'nullable|numeric|min:0'
        ]);

        $sellingPrice = $request->selling_price;
        
        // If selling_price is empty or null, set it to null (will use purchase price as default)
        if (empty($sellingPrice)) {
            $sellingPrice = null;
        }

        $product->update([
            'selling_price' => $sellingPrice
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Selling price updated successfully',
            'selling_price' => $sellingPrice,
            'purchase_price' => number_format($product->price, 2),
            'effective_selling_price' => number_format($product->getEffectiveSellingPrice(), 2)
        ]);
    }
}
