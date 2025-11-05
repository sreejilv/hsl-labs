<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'doctor_id' => User::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date(),
            'address' => $this->faker->address(),
            'emergency_contact' => $this->faker->name(),
            'emergency_phone' => $this->faker->phoneNumber(),
            'medical_history' => $this->faker->paragraph(),
            'allergies' => $this->faker->optional()->sentence(),
            'current_medications' => $this->faker->optional()->sentence(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}