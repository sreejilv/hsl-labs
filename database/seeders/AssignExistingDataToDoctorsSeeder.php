<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\StaffDetail;

class AssignExistingDataToDoctorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all surgeons (doctors)
        $surgeons = User::role('surgeon')->get();
        
        if ($surgeons->isEmpty()) {
            $this->command->info('No surgeons found. Please create surgeons first.');
            return;
        }

        // Assign existing patients to the first surgeon
        $patients = Patient::whereNull('doctor_id')->get();
        if ($patients->isNotEmpty()) {
            $firstSurgeon = $surgeons->first();
            foreach ($patients as $patient) {
                $patient->update(['doctor_id' => $firstSurgeon->id]);
            }
            $this->command->info('Assigned ' . $patients->count() . ' patients to Dr. ' . $firstSurgeon->first_name . ' ' . $firstSurgeon->last_name);
        }

        // Assign existing staff to surgeons (distribute evenly)
        $staff = StaffDetail::whereNull('doctor_id')->get();
        if ($staff->isNotEmpty()) {
            $surgeonIndex = 0;
            foreach ($staff as $staffMember) {
                $surgeon = $surgeons[$surgeonIndex % $surgeons->count()];
                $staffMember->update(['doctor_id' => $surgeon->id]);
                $surgeonIndex++;
            }
            $this->command->info('Assigned ' . $staff->count() . ' staff members to surgeons');
        }
    }
}
