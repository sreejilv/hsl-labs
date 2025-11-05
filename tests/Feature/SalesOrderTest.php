<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function staff_can_create_sales_order_for_assigned_doctors_patients(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 10]);

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), [
                'patient_id' => $patient->id,
                'notes' => 'Test order',
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                        'unit_price' => $product->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales_orders', [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('sales_order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Check stock was decremented
        $product->refresh();
        $this->assertEquals(8, $product->stock);
    }

    /** @test */
    public function staff_cannot_create_order_for_other_doctors_patients(): void
    {
        // Arrange
        $doctor1 = $this->createSurgeon(['email' => 'doctor1@test.com']);
        $doctor2 = $this->createSurgeon(['email' => 'doctor2@test.com']);
        $staff = $this->createStaff($doctor1);
        $patient = $this->createPatient($doctor2); // Different doctor's patient
        $product = $this->createProduct();

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), [
                'patient_id' => $patient->id,
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'unit_price' => $product->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        // Assert
        $this->assertAuthorizationError($response);
    }

    /** @test */
    public function sales_order_creation_fails_with_insufficient_stock(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 1]);

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), [
                'patient_id' => $patient->id,
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5, // More than available stock
                        'unit_price' => $product->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('sales_orders', [
            'patient_id' => $patient->id,
        ]);

        // Stock should remain unchanged
        $product->refresh();
        $this->assertEquals(1, $product->stock);
    }

    /** @test */
    public function sales_order_validation_requires_valid_data(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);

        // Act & Assert - Missing required fields
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), []);

        $this->assertValidationError($response, ['patient_id', 'products']);

        // Act & Assert - Invalid product data
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), [
                'patient_id' => 999, // Non-existent patient
                'products' => [
                    [
                        'product_id' => 999, // Non-existent product
                        'quantity' => 0, // Invalid quantity
                        'unit_price' => -10, // Invalid price
                    ]
                ]
            ]);

        $this->assertValidationError($response);
    }

    /** @test */
    public function concurrent_orders_handle_stock_correctly(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff1 = $this->createStaff($doctor, ['email' => 'staff1@test.com']);
        $staff2 = $this->createStaff($doctor, ['email' => 'staff2@test.com']);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 5]);

        // Act - Simulate concurrent orders
        $orderData = [
            'patient_id' => $patient->id,
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => $product->getEffectiveSellingPrice(),
                ]
            ]
        ];

        $response1 = $this->actingAs($staff1)
            ->post(route('medical.sales-orders.store'), $orderData);

        $response2 = $this->actingAs($staff2)
            ->post(route('medical.sales-orders.store'), $orderData);

        // Assert - First order should succeed
        $response1->assertRedirect();
        $response1->assertSessionHas('success');

        // Second order should fail due to insufficient stock
        $response2->assertRedirect();
        $response2->assertSessionHas('error');

        // Final stock should be 2 (5 - 3 from first order)
        $product->refresh();
        $this->assertEquals(2, $product->stock);

        // Only one order should exist
        $this->assertEquals(1, SalesOrder::count());
    }

    /** @test */
    public function surgeon_can_view_all_sales_orders(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct();

        // Create a sales order
        $salesOrder = SalesOrder::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'status' => 'completed',
            'total_amount' => 100.00,
        ]);

        // Act
        $response = $this->actingAs($doctor)
            ->get(route('medical.sales-orders.index'));

        // Assert
        $response->assertOk();
        $response->assertSee($salesOrder->id);
        $response->assertSee($patient->name);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_sales_orders(): void
    {
        // Act
        $response = $this->get(route('medical.sales-orders.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function sales_order_calculates_total_correctly(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product1 = $this->createProduct(['selling_price' => 50.00, 'stock' => 10]);
        $product2 = $this->createProduct(['selling_price' => 75.00, 'stock' => 10]);

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.sales-orders.store'), [
                'patient_id' => $patient->id,
                'products' => [
                    [
                        'product_id' => $product1->id,
                        'quantity' => 2,
                        'unit_price' => $product1->getEffectiveSellingPrice(),
                    ],
                    [
                        'product_id' => $product2->id,
                        'quantity' => 1,
                        'unit_price' => $product2->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        // Assert
        $response->assertRedirect();
        
        $salesOrder = SalesOrder::first();
        $expectedTotal = (2 * 50.00) + (1 * 75.00); // 175.00
        
        $this->assertEquals($expectedTotal, $salesOrder->total_amount);
    }
}