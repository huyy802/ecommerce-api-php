<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'phone_number' => $this->faker->phoneNumber,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birthday' => $this->faker->date(),
            'address' => [
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
            ],
        ];
    }
}
