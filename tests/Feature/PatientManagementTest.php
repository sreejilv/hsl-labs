<?php

namespace Tests\Feature;

use App\Models\Patient;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function surgeon_can_create_patient(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('medical.patients.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'address' => '123 Main St',
                'emergency_contact' => 'Jane Doe',
                'emergency_phone' => '0987654321',
                'medical_history' => 'No significant history',
                'allergies' => 'None',
                'current_medications' => 'None',
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'doctor_id' => $surgeon->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function staff_cannot_create_patients(): void
    {
        // Arrange
        $doctor = $this->createSurgeon();
        $staff = $this->createStaff($doctor);

        // Act
        $response = $this->actingAs($staff)
            ->post(route('medical.patients.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
            ]);

        // Assert
        $this->assertAuthorizationError($response);
    }

    /** @test */
    public function patient_creation_validates_required_fields(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('medical.patients.store'), []);

        // Assert
        $this->assertValidationError($response, [
            'name', 'email', 'phone', 'date_of_birth'
        ]);
    }

    /** @test */
    public function patient_email_must_be_unique(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $existingPatient = $this->createPatient($surgeon, [
            'email' => 'existing@example.com'
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->post(route('medical.patients.store'), [
                'name' => 'John Doe',
                'email' => 'existing@example.com', // Duplicate email
                'phone' => '1234567890',
                'date_of_birth' => '1990-01-01',
            ]);

        // Assert
        $this->assertValidationError($response, ['email']);
    }

    /** @test */
    public function surgeon_can_view_only_their_patients(): void
    {
        // Arrange
        $surgeon1 = $this->createSurgeon(['email' => 'surgeon1@test.com']);
        $surgeon2 = $this->createSurgeon(['email' => 'surgeon2@test.com']);
        
        $patient1 = $this->createPatient($surgeon1, ['name' => 'Patient 1']);
        $patient2 = $this->createPatient($surgeon2, ['name' => 'Patient 2']);

        // Act
        $response = $this->actingAs($surgeon1)
            ->get(route('medical.patients.index'));

        // Assert
        $response->assertOk();
        $response->assertSee('Patient 1');
        $response->assertDontSee('Patient 2');
    }

    /** @test */
    public function staff_can_view_only_assigned_doctors_patients(): void
    {
        // Arrange
        $doctor1 = $this->createSurgeon(['email' => 'doctor1@test.com']);
        $doctor2 = $this->createSurgeon(['email' => 'doctor2@test.com']);
        $staff = $this->createStaff($doctor1);
        
        $patient1 = $this->createPatient($doctor1, ['name' => 'Patient 1']);
        $patient2 = $this->createPatient($doctor2, ['name' => 'Patient 2']);

        // Act
        $response = $this->actingAs($staff)
            ->get(route('medical.patients.index'));

        // Assert
        $response->assertOk();
        $response->assertSee('Patient 1');
        $response->assertDontSee('Patient 2');
    }

    /** @test */
    public function surgeon_can_update_patient_information(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $patient = $this->createPatient($surgeon);

        // Act
        $response = $this->actingAs($surgeon)
            ->put(route('medical.patients.update', $patient), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '1111111111',
                'date_of_birth' => $patient->date_of_birth,
                'address' => 'Updated Address',
                'emergency_contact' => $patient->emergency_contact,
                'emergency_phone' => $patient->emergency_phone,
                'medical_history' => 'Updated history',
                'allergies' => $patient->allergies,
                'current_medications' => $patient->current_medications,
            ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '1111111111',
            'address' => 'Updated Address',
            'medical_history' => 'Updated history',
        ]);
    }

    /** @test */
    public function surgeon_cannot_update_other_doctors_patients(): void
    {
        // Arrange
        $surgeon1 = $this->createSurgeon(['email' => 'surgeon1@test.com']);
        $surgeon2 = $this->createSurgeon(['email' => 'surgeon2@test.com']);
        $patient = $this->createPatient($surgeon2);

        // Act
        $response = $this->actingAs($surgeon1)
            ->put(route('medical.patients.update', $patient), [
                'name' => 'Hacked Name',
                'email' => $patient->email,
                'phone' => $patient->phone,
                'date_of_birth' => $patient->date_of_birth,
            ]);

        // Assert
        $this->assertAuthorizationError($response);
    }

    /** @test */
    public function patient_can_be_deactivated(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $patient = $this->createPatient($surgeon);

        // Act
        $response = $this->actingAs($surgeon)
            ->patch(route('medical.patients.toggle-status', $patient));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $patient->refresh();
        $this->assertEquals('inactive', $patient->status);
    }

    /** @test */
    public function inactive_patients_are_filtered_from_active_scope(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $activePatient = $this->createPatient($surgeon, ['status' => 'active']);
        $inactivePatient = $this->createPatient($surgeon, [
            'status' => 'inactive',
            'email' => 'inactive@test.com'
        ]);

        // Act
        $activePatients = Patient::active()->get();

        // Assert
        $this->assertTrue($activePatients->contains($activePatient));
        $this->assertFalse($activePatients->contains($inactivePatient));
    }

    /** @test */
    public function patient_search_functionality_works(): void
    {
        // Arrange
        $surgeon = $this->createSurgeon();
        $patient1 = $this->createPatient($surgeon, [
            'name' => 'John Smith',
            'email' => 'john@test.com'
        ]);
        $patient2 = $this->createPatient($surgeon, [
            'name' => 'Jane Doe',
            'email' => 'jane@test.com'
        ]);

        // Act
        $response = $this->actingAs($surgeon)
            ->get(route('medical.patients.index', ['search' => 'John']));

        // Assert
        $response->assertOk();
        $response->assertSee('John Smith');
        $response->assertDontSee('Jane Doe');
    }
}