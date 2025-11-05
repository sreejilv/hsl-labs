<?php

namespace Tests\Feature;

use App\Models\RecurringOrder;
use App\Models\RecurringOrderItem;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecurringOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function staff_can_create_recurring_order(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 100]);

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.recurring-orders.store'), [
                'patient_id' => $patient->id,
                'duration_months' => 6,
                'start_date' => now()->format('Y-m-d'),
                'day_of_month' => 15,
                'notes' => 'Monthly treatment plan',
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

        $this->assertDatabaseHas('recurring_orders', [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'duration_months' => 6,
            'remaining_months' => 6,
            'day_of_month' => 15,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('recurring_order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function recurring_order_validation_enforces_duration_range(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct();

        // Act & Assert - Duration too low
        $response = $this->actingAs($staff)
            ->post(route('medical.recurring-orders.store'), [
                'patient_id' => $patient->id,
                'duration_months' => 1, // Below minimum of 2
                'start_date' => now()->format('Y-m-d'),
                'day_of_month' => 15,
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'unit_price' => $product->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        $this->assertValidationError($response, ['duration_months']);

        // Act & Assert - Duration too high
        $response = $this->actingAs($staff)
            ->post(route('medical.recurring-orders.store'), [
                'patient_id' => $patient->id,
                'duration_months' => 13, // Above maximum of 12
                'start_date' => now()->format('Y-m-d'),
                'day_of_month' => 15,
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                        'unit_price' => $product->getEffectiveSellingPrice(),
                    ]
                ]
            ]);

        $this->assertValidationError($response, ['duration_months']);
    }

    /** @test */
    public function recurring_order_processing_creates_sales_order(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 50]);

        $recurringOrder = RecurringOrder::create([
            'recurring_order_number' => 'RO123456',
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'frequency' => 'monthly',
            'duration_months' => 6,
            'remaining_months' => 6,
            'start_date' => now()->subDays(30),
            'next_due_date' => now()->subDays(1), // Due yesterday
            'day_of_month' => now()->day,
            'status' => 'active',
            'total_amount' => 100.00,
        ]);

        RecurringOrderItem::create([
            'recurring_order_id' => $recurringOrder->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->getEffectiveSellingPrice(),
            'total_price' => 2 * $product->getEffectiveSellingPrice(),
        ]);

        // Act
        $salesOrder = $recurringOrder->processRecurringOrder();

        // Assert
        $this->assertInstanceOf(SalesOrder::class, $salesOrder);
        $this->assertEquals('completed', $salesOrder->status);
        $this->assertEquals($recurringOrder->id, $salesOrder->recurring_order_id);

        // Check stock was decremented
        $product->refresh();
        $this->assertEquals(48, $product->stock);

        // Check recurring order was updated
        $recurringOrder->refresh();
        $this->assertEquals(5, $recurringOrder->remaining_months);
        $this->assertNotNull($recurringOrder->last_processed_at);
    }

    /** @test */
    public function recurring_order_processing_fails_with_insufficient_stock(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 1]); // Low stock

        $recurringOrder = RecurringOrder::create([
            'recurring_order_number' => 'RO123456',
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'frequency' => 'monthly',
            'duration_months' => 6,
            'remaining_months' => 6,
            'start_date' => now()->subDays(30),
            'next_due_date' => now()->subDays(1),
            'day_of_month' => now()->day,
            'status' => 'active',
            'total_amount' => 100.00,
        ]);

        RecurringOrderItem::create([
            'recurring_order_id' => $recurringOrder->id,
            'product_id' => $product->id,
            'quantity' => 5, // More than available stock
            'unit_price' => $product->getEffectiveSellingPrice(),
            'total_price' => 5 * $product->getEffectiveSellingPrice(),
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');

        $recurringOrder->processRecurringOrder();

        // Stock should remain unchanged
        $product->refresh();
        $this->assertEquals(1, $product->stock);
    }

    /** @test */
    public function staff_cannot_access_other_doctors_recurring_orders(): void
    {
        // Arrange
        $doctor1 = $this->createSurgeon(['email' => 'doctor1@test.com']);
        $doctor2 = $this->createSurgeon(['email' => 'doctor2@test.com']);
        $staff1 = $this->createStaff($doctor1, ['email' => 'staff1@test.com']);
        $patient2 = $this->createPatient($doctor2);

        $recurringOrder = RecurringOrder::create([
            'recurring_order_number' => 'RO123456',
            'doctor_id' => $doctor2->id,
            'patient_id' => $patient2->id,
            'staff_id' => $doctor2->id, // Different doctor's order
            'frequency' => 'monthly',
            'duration_months' => 6,
            'remaining_months' => 6,
            'start_date' => now(),
            'next_due_date' => now()->addDays(30),
            'day_of_month' => 15,
            'status' => 'active',
            'total_amount' => 100.00,
        ]);

        // Act
        $response = $this->actingAs($staff1)
            ->get(route('medical.recurring-orders.show', $recurringOrder));

        // Assert
        $this->assertAuthorizationError($response);
    }

    /** @test */
    public function recurring_order_can_be_paused_and_resumed(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);

        $recurringOrder = RecurringOrder::create([
            'recurring_order_number' => 'RO123456',
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'frequency' => 'monthly',
            'duration_months' => 6,
            'remaining_months' => 6,
            'start_date' => now(),
            'next_due_date' => now()->addDays(30),
            'day_of_month' => 15,
            'status' => 'active',
            'total_amount' => 100.00,
        ]);

        // Act - Pause
        $response = $this->actingAs($staff)
            ->patch(route('medical.recurring-orders.pause', $recurringOrder));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $recurringOrder->refresh();
        $this->assertEquals('paused', $recurringOrder->status);

        // Act - Resume
        $response = $this->actingAs($staff)
            ->patch(route('medical.recurring-orders.resume', $recurringOrder));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $recurringOrder->refresh();
        $this->assertEquals('active', $recurringOrder->status);
    }

    /** @test */
    public function recurring_order_completion_marks_as_completed(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);
        $product = $this->createProduct(['stock' => 50]);

        $recurringOrder = RecurringOrder::create([
            'recurring_order_number' => 'RO123456',
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'staff_id' => $staff->id,
            'frequency' => 'monthly',
            'duration_months' => 1,
            'remaining_months' => 1, // Last month
            'start_date' => now()->subDays(30),
            'next_due_date' => now()->subDays(1),
            'day_of_month' => now()->day,
            'status' => 'active',
            'total_amount' => 100.00,
        ]);

        RecurringOrderItem::create([
            'recurring_order_id' => $recurringOrder->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->getEffectiveSellingPrice(),
            'total_price' => $product->getEffectiveSellingPrice(),
        ]);

        // Act
        $salesOrder = $recurringOrder->processRecurringOrder();

        // Assert
        $recurringOrder->refresh();
        $this->assertEquals('completed', $recurringOrder->status);
        $this->assertEquals(0, $recurringOrder->remaining_months);
    }

    /** @test */
    public function recurring_order_calculates_next_due_date_correctly(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);
        $patient = $this->createPatient($doctor);

        $startDate = Carbon::create(2024, 1, 15); // January 15th
        $dayOfMonth = 20;

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.recurring-orders.store'), [
                'patient_id' => $patient->id,
                'duration_months' => 3,
                'start_date' => $startDate->format('Y-m-d'),
                'day_of_month' => $dayOfMonth,
                'products' => [
                    [
                        'product_id' => $this->createProduct()->id,
                        'quantity' => 1,
                        'unit_price' => 100,
                    ]
                ]
            ]);

        // Assert
        $recurringOrder = RecurringOrder::first();
        $expectedNextDueDate = Carbon::create(2024, 1, 20); // January 20th (first due date)
        
        $this->assertEquals($expectedNextDueDate->format('Y-m-d'), $recurringOrder->next_due_date->format('Y-m-d'));
    }
}