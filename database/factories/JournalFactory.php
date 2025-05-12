<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Journal;
use App\Models\Publisher;

class JournalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Journal::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'price' => fake()->randomFloat(0, 0, 9999999999.),
            'scope' => '{}',
            'path' => fake()->word(),
            'thumbnail' => fake()->word(),
            'publish_months' => '{}',
            'publisher_id' => Publisher::factory(),
        ];
    }
}
