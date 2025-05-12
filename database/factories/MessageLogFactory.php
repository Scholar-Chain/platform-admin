<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\MessageLog;

class MessageLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MessageLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'channel' => fake()->word(),
            'target' => fake()->word(),
            'message' => fake()->text(),
            'media' => fake()->word(),
            'response' => '{}',
            'success' => fake()->boolean(),
        ];
    }
}
