<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\DoctorDetail;
use App\Models\StaffDetail;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $surgeonRole = Role::firstOrCreate(['name' => 'surgeon']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Create default admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role to the user if not already assigned
        if (!$admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        // Create sample surgeon user if it doesn't exist
        $surgeon = User::firstOrCreate(
            ['email' => 'surgeon@example.com'],
            [
                'name' => 'Dr. John Surgeon',
                'password' => Hash::make('surgeon123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign surgeon role to the user if not already assigned
        if (!$surgeon->hasRole($surgeonRole)) {
            $surgeon->assignRole($surgeonRole);
        }

        // Create doctor details for the surgeon if it doesn't exist
        DoctorDetail::firstOrCreate(
            ['user_id' => $surgeon->id],
            [
                'clinic_name' => 'City Medical Center',
                'doctor_name' => 'Dr. John Surgeon',
                'address' => '123 Medical Plaza, Downtown District, City 12345',
                'phone' => '+1 (555) 123-4567',
                'documents' => null, // No documents initially
                'is_active' => true,
            ]
        );

        // Create sample staff user if it doesn't exist
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff Member',
                'first_name' => 'Jane',
                'last_name' => 'Staff',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
                'phone' => '+1 (555) 987-6543',
                'date_of_birth' => '1990-05-15',
                'gender' => 'female',
                'address' => '456 Staff Street, Medical District, City 12345',
                'is_active' => true,
            ]
        );

        // Assign staff role to the user if not already assigned
        if (!$staff->hasRole($staffRole)) {
            $staff->assignRole($staffRole);
        }

        // Create staff details linking the staff to the surgeon
        StaffDetail::firstOrCreate(
            ['user_id' => $staff->id],
            [
                'staff_id' => 'STF001',
                'department' => 'Medical',
                'position' => 'Medical Assistant',
                'hire_date' => now()->subMonths(6),
                'salary' => 45000.00,
                'shift' => 'day',
                'is_active' => true,
                'emergency_contact_name' => 'John Staff (Husband)',
                'emergency_contact_phone' => '+1 (555) 555-1234',
                'qualifications' => ['Medical Assistant Certification', 'CPR Certified'],
                'notes' => 'Experienced medical assistant with excellent patient care skills.',
                'created_by' => $surgeon->id,
                'doctor_id' => $surgeon->id, // This is the crucial link!
            ]
        );
    }
}
