<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'client' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'budget' => $this->faker->randomFloat(2, 50000, 2000000),
            'status' => 'draft',
            'progress' => $this->faker->numberBetween(0, 100),
            'redirect_to_plan_request' => false,
            'agency' => null,
            'commercial_name' => null,
            'facade_color' => null,
            'garage_type' => null,
            'garage_dimensions' => null,
            'heating_type' => null,
            'house_dimensions' => null,
            'house_type' => null,
            'kitchen_type' => null,
            'livable_area' => null,
            'living_room_size' => null,
            'bedrooms_count' => null,
            'bedrooms_size' => null,
            'bathrooms_count' => null,
            'basement_type' => null,
            'roof_angle' => null,
            'tile_color' => null,
            'window_type' => null,
            'additional_options' => null,
            'start_date' => null,
            'deadline' => null,
        ];
    }
}
