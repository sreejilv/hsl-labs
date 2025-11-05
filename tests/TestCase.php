<?php

namespace Tests;

use App\Models\User;
use App\Models\Patient;
use App\Models\Product;
use App\Models\StaffDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $this->createRoles();
        
        // Seed basic permissions
        $this->artisan('db:seed', ['--class' => 'RolesAndAdminSeeder']);
    }

    protected function createRoles(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'surgeon']);
        Role::create(['name' => 'staff']);
    }

    protected function createSurgeon(array $attributes = []): User
    {
        $surgeon = User::factory()->create(array_merge([
            'email' => 'surgeon@test.com',
            'name' => 'Dr. Test Surgeon',
            'first_name' => 'Test',
            'last_name' => 'Surgeon',
        ], $attributes));

        $surgeon->assignRole('surgeon');
        
        return $surgeon;
    }

    protected function createStaff(User $doctor = null, array $attributes = []): User
    {
        if (!$doctor) {
            $doctor = $this->createSurgeon();
        }

        $staff = User::factory()->create(array_merge([
            'email' => 'staff@test.com',
            'name' => 'Test Staff',
            'first_name' => 'Test',
            'last_name' => 'Staff',
        ], $attributes));

        $staff->assignRole('staff');

        // Create staff detail linking to doctor
        StaffDetail::create([
            'user_id' => $staff->id,
            'doctor_id' => $doctor->id,
            'position' => 'Medical Assistant',
            'hire_date' => now(),
            'is_active' => true,
        ]);

        return $staff;
    }

    protected function createPatient(User $doctor = null, array $attributes = []): Patient
    {
        if (!$doctor) {
            $doctor = $this->createSurgeon();
        }

        return Patient::factory()->create(array_merge([
            'doctor_id' => $doctor->id,
            'name' => 'Test Patient',
            'email' => 'patient@test.com',
            'phone' => '1234567890',
            'status' => 'active',
        ], $attributes));
    }

    protected function createProduct(array $attributes = []): Product
    {
        return Product::factory()->create(array_merge([
            'name' => 'Test Product',
            'description' => 'Test product description',
            'purchase_price' => 100.00,
            'selling_price' => 120.00,
            'stock' => 50,
            'status' => 'active',
        ], $attributes));
    }

    protected function assertDatabaseHasModel($model, array $attributes = [])
    {
        $this->assertDatabaseHas($model->getTable(), array_merge([
            'id' => $model->id,
        ], $attributes));
    }

    protected function assertAuthorizationError($response)
    {
        $response->assertStatus(403);
    }

    protected function assertValidationError($response, array $fields = [])
    {
        $response->assertStatus(422);
        
        if (!empty($fields)) {
            $response->assertJsonValidationErrors($fields);
        }
    }
}
