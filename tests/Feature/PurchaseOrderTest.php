<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function surgeon_can_create_purchase_order(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $product = $this->createProduct();

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('purchase-orders.store'), [
                'supplier_name' => 'MedSupply Inc',
                'supplier_contact' => 'contact@medsupply.com',
                'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 50,
                        'unit_cost' => 10.00,
                    ]
                ]
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_orders', [
            'created_by' => $surgeon->id,
            'supplier_name' => 'MedSupply Inc',
            'status' => 'pending',
            'total_amount' => 500.00,
        ]);

        $this->assertDatabaseHas('purchase_order_items', [
            'product_id' => $product->id,
            'quantity' => 50,
            'unit_cost' => 10.00,
            'total_cost' => 500.00,
        ]);
    }

    /** @test */
    public function purchase_order_validation_requires_valid_data(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('purchase-orders.store'), [
                'supplier_name' => '', // Required
                'items' => [] // Must have at least one item
            ]);

        // Assert
        $this->assertValidationError($response, [
            'supplier_name',
            'supplier_contact',
            'expected_delivery_date',
            'items'
        ]);
    }

    /** @test */
    public function purchase_order_items_validation_requires_valid_products(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('purchase-orders.store'), [
                'supplier_name' => 'Valid Supplier',
                'supplier_contact' => 'contact@example.com',
                'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => 999999, // Non-existent product
                        'quantity' => -5, // Invalid quantity
                        'unit_cost' => -10.00, // Invalid cost
                    ]
                ]
            ]);

        // Assert
        $this->assertValidationError($response, [
            'items.0.product_id',
            'items.0.quantity',
            'items.0.unit_cost'
        ]);
    }

    /** @test */
    public function staff_cannot_create_purchase_orders(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $product = $this->createProduct();

        // Act
        $response = $this->actingAs($staff)
            ->post(route('purchase-orders.store'), [
                'supplier_name' => 'MedSupply Inc',
                'supplier_contact' => 'contact@medsupply.com',
                'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 50,
                        'unit_cost' => 10.00,
                    ]
                ]
            ]);

        // Assert
        $this->assertAuthorizationError($response);
    }

    /** @test */
    public function purchase_order_calculates_total_correctly(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $product1 = $this->createProduct(['name' => 'Product 1']);
        $product2 = $this->createProduct(['name' => 'Product 2']);

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('purchase-orders.store'), [
                'supplier_name' => 'MedSupply Inc',
                'supplier_contact' => 'contact@medsupply.com',
                'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
                'items' => [
                    [
                        'product_id' => $product1->id,
                        'quantity' => 10,
                        'unit_cost' => 15.50,
                    ],
                    [
                        'product_id' => $product2->id,
                        'quantity' => 5,
                        'unit_cost' => 25.00,
                    ]
                ]
            ]);

        // Assert
        $expectedTotal = (10 * 15.50) + (5 * 25.00); // 155 + 125 = 280
        
        $this->assertDatabaseHas('purchase_orders', [
            'total_amount' => $expectedTotal,
        ]);
    }

    /** @test */
    public function purchase_order_can_be_approved(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $product = $this->createProduct();
        
        $purchaseOrder = PurchaseOrder::create([
            'created_by' => $surgeon->id,
            'supplier_name' => 'MedSupply Inc',
            'supplier_contact' => 'contact@medsupply.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'pending',
            'total_amount' => 500.00,
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->patch(route('purchase-orders.approve', $purchaseOrder));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $purchaseOrder->refresh();
        $this->assertEquals('approved', $purchaseOrder->status);
        $this->assertNotNull($purchaseOrder->approved_at);
        $this->assertEquals($surgeon->id, $purchaseOrder->approved_by);
    }

    /** @test */
    public function purchase_order_can_be_received_and_updates_stock(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $product = $this->createProduct(['stock_quantity' => 100]);
        
        $purchaseOrder = PurchaseOrder::create([
            'created_by' => $surgeon->id,
            'supplier_name' => 'MedSupply Inc',
            'supplier_contact' => 'contact@medsupply.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'approved',
            'total_amount' => 500.00,
            'approved_at' => now(),
            'approved_by' => $surgeon->id,
        ]);

        $purchaseOrderItem = PurchaseOrderItem::create([
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'quantity' => 50,
            'unit_cost' => 10.00,
            'total_cost' => 500.00,
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->patch(route('purchase-orders.receive', $purchaseOrder), [
                'received_quantities' => [
                    $purchaseOrderItem->id => 45, // Received 45 out of 50
                ]
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $purchaseOrder->refresh();
        $product->refresh();
        
        $this->assertEquals('received', $purchaseOrder->status);
        $this->assertNotNull($purchaseOrder->received_at);
        $this->assertEquals($surgeon->id, $purchaseOrder->received_by);
        $this->assertEquals(145, $product->stock_quantity); // 100 + 45
    }

    /** @test */
    public function purchase_order_can_be_cancelled(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        
        $purchaseOrder = PurchaseOrder::create([
            'created_by' => $surgeon->id,
            'supplier_name' => 'MedSupply Inc',
            'supplier_contact' => 'contact@medsupply.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'pending',
            'total_amount' => 500.00,
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->patch(route('purchase-orders.cancel', $purchaseOrder), [
                'cancellation_reason' => 'Supplier no longer available'
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $purchaseOrder->refresh();
        $this->assertEquals('cancelled', $purchaseOrder->status);
        $this->assertEquals('Supplier no longer available', $purchaseOrder->cancellation_reason);
        $this->assertNotNull($purchaseOrder->cancelled_at);
    }

    /** @test */
    public function surgeon_can_view_all_purchase_orders(): void
    {
        // Arrange
        $surgeon1 = $this->createSurgeon(['email' => 'surgeon1@test.com']);
        $surgeon2 = $this->createSurgeon(['email' => 'surgeon2@test.com']);
        
        $po1 = PurchaseOrder::create([
            'created_by' => $surgeon1->id,
            'supplier_name' => 'Supplier 1',
            'supplier_contact' => 'contact1@example.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'pending',
            'total_amount' => 100.00,
        ]);

        $po2 = PurchaseOrder::create([
            'created_by' => $surgeon2->id,
            'supplier_name' => 'Supplier 2',
            'supplier_contact' => 'contact2@example.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'pending',
            'total_amount' => 200.00,
        ]);

        // Act
        $response = $this->actingAs($surgeon1)
            ->get(route('purchase-orders.index'));

        // Assert
        $response->assertOk();
        $response->assertSee('Supplier 1');
        $response->assertSee('Supplier 2'); // Surgeons can see all POs for oversight
    }

    /** @test */
    public function purchase_order_filtering_by_status_works(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        
        $pendingPO = PurchaseOrder::create([
            'created_by' => $surgeon->id,
            'supplier_name' => 'Pending Supplier',
            'supplier_contact' => 'pending@example.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'pending',
            'total_amount' => 100.00,
        ]);

        $approvedPO = PurchaseOrder::create([
            'created_by' => $surgeon->id,
            'supplier_name' => 'Approved Supplier',
            'supplier_contact' => 'approved@example.com',
            'expected_delivery_date' => now()->addDays(7),
            'status' => 'approved',
            'total_amount' => 200.00,
            'approved_at' => now(),
            'approved_by' => $surgeon->id,
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->get(route('purchase-orders.index', ['status' => 'pending']));

        // Assert
        $response->assertOk();
        $response->assertSee('Pending Supplier');
        $response->assertDontSee('Approved Supplier');
    }
}