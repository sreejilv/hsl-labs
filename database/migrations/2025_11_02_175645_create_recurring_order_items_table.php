<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_order_id')->constrained('recurring_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Price at time of recurring order creation
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            
            $table->index(['recurring_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_order_items');
    }
};
