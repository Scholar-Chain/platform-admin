<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Publisher;
use App\Models\User;

class PublisherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Publisher::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'publishing_name' => fake()->word(),
            'address' => fake()->text(),
            'website' => fake()->word(),
            'ojs_driver_url' => fake()->word(),
            'user_id' => User::factory(),
        ];
    }
}
