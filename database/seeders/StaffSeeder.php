<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StaffDetail;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure staff role exists
        if (!Role::where('name', 'staff')->exists()) {
            Role::create(['name' => 'staff']);
        }

        $staffMembers = [
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@hsl-labs.com',
                'phone' => '+1-555-0101',
                'date_of_birth' => '1985-03-15',
                'gender' => 'female',
                'address' => '123 Main St, Springfield, IL 62701',
                'staff_id' => 'SJ001',
                'department' => 'Emergency',
                'position' => 'Registered Nurse',
                'hire_date' => '2020-01-15',
                'salary' => 65000,
                'shift' => 'day',
                'emergency_contact_name' => 'John Johnson',
                'emergency_contact_phone' => '+1-555-0102',
                'qualifications' => 'RN License, BLS Certification, ACLS Certification',
                'notes' => 'Excellent performance in emergency situations.',
                'is_active' => true,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@hsl-labs.com',
                'phone' => '+1-555-0201',
                'date_of_birth' => '1992-07-22',
                'gender' => 'male',
                'address' => '456 Oak Ave, Springfield, IL 62702',
                'staff_id' => 'MC002',
                'department' => 'Laboratory',
                'position' => 'Lab Technician',
                'hire_date' => '2021-06-01',
                'salary' => 48000,
                'shift' => 'day',
                'emergency_contact_name' => 'Lisa Chen',
                'emergency_contact_phone' => '+1-555-0202',
                'qualifications' => 'Medical Laboratory Science Degree, ASCP Certification',
                'notes' => 'Specializes in blood chemistry analysis.',
                'is_active' => true,
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Rodriguez',
                'email' => 'emily.rodriguez@hsl-labs.com',
                'phone' => '+1-555-0301',
                'date_of_birth' => '1988-11-30',
                'gender' => 'female',
                'address' => '789 Pine St, Springfield, IL 62703',
                'staff_id' => 'ER003',
                'department' => 'Radiology',
                'position' => 'Radiologic Technologist',
                'hire_date' => '2019-09-10',
                'salary' => 58000,
                'shift' => 'rotating',
                'emergency_contact_name' => 'Carlos Rodriguez',
                'emergency_contact_phone' => '+1-555-0302',
                'qualifications' => 'ARRT Certification, CT and MRI Training',
                'notes' => 'Expert in advanced imaging techniques.',
                'is_active' => true,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Williams',
                'email' => 'david.williams@hsl-labs.com',
                'phone' => '+1-555-0401',
                'date_of_birth' => '1995-02-14',
                'gender' => 'male',
                'address' => '321 Elm St, Springfield, IL 62704',
                'staff_id' => 'DW004',
                'department' => 'Pharmacy',
                'position' => 'Pharmacy Technician',
                'hire_date' => '2022-03-20',
                'salary' => 42000,
                'shift' => 'day',
                'emergency_contact_name' => 'Mary Williams',
                'emergency_contact_phone' => '+1-555-0402',
                'qualifications' => 'Pharmacy Technician Certification, IV Therapy Training',
                'notes' => 'Excellent attention to detail in medication preparation.',
                'is_active' => true,
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Taylor',
                'email' => 'jessica.taylor@hsl-labs.com',
                'phone' => '+1-555-0501',
                'date_of_birth' => '1990-05-08',
                'gender' => 'female',
                'address' => '654 Maple Ave, Springfield, IL 62705',
                'staff_id' => 'JT005',
                'department' => 'ICU',
                'position' => 'Critical Care Nurse',
                'hire_date' => '2018-11-05',
                'salary' => 72000,
                'shift' => 'night',
                'emergency_contact_name' => 'Robert Taylor',
                'emergency_contact_phone' => '+1-555-0502',
                'qualifications' => 'RN License, CCRN Certification, Ventilator Management',
                'notes' => 'Exceptional skills in critical care management.',
                'is_active' => true,
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Brown',
                'email' => 'kevin.brown@hsl-labs.com',
                'phone' => '+1-555-0601',
                'date_of_birth' => '1987-09-18',
                'gender' => 'male',
                'address' => '987 Cedar St, Springfield, IL 62706',
                'staff_id' => 'KB006',
                'department' => 'Administration',
                'position' => 'Administrative Assistant',
                'hire_date' => '2020-08-12',
                'salary' => 38000,
                'shift' => 'day',
                'emergency_contact_name' => 'Amanda Brown',
                'emergency_contact_phone' => '+1-555-0602',
                'qualifications' => 'Administrative Certificate, Medical Terminology',
                'notes' => 'Efficient in managing departmental documentation.',
                'is_active' => true,
            ],
        ];

        foreach ($staffMembers as $staffData) {
            // Create user
            $user = User::create([
                'name' => $staffData['first_name'] . ' ' . $staffData['last_name'],
                'first_name' => $staffData['first_name'],
                'last_name' => $staffData['last_name'],
                'email' => $staffData['email'],
                'password' => Hash::make('password123'),
                'phone' => $staffData['phone'],
                'date_of_birth' => $staffData['date_of_birth'],
                'gender' => $staffData['gender'],
                'address' => $staffData['address'],
            ]);

            // Assign staff role
            $user->assignRole('staff');

            // Create staff detail
            StaffDetail::create([
                'user_id' => $user->id,
                'staff_id' => $staffData['staff_id'],
                'department' => null, // Set to null since we removed department from forms
                'position' => $staffData['position'],
                'hire_date' => $staffData['hire_date'],
                'salary' => $staffData['salary'],
                'shift' => $staffData['shift'],
                'emergency_contact_name' => $staffData['emergency_contact_name'],
                'emergency_contact_phone' => $staffData['emergency_contact_phone'],
                'qualifications' => $staffData['qualifications'],
                'notes' => $staffData['notes'],
                'is_active' => $staffData['is_active'],
            ]);
        }

        $this->command->info('Staff members seeded successfully!');
    }
}
