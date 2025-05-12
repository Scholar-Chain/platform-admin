<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\SyncErrorLog;

class SyncErrorLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SyncErrorLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->randomNumber(),
            'assoc' => fake()->word(),
            'payload' => '{}',
            'error_message' => fake()->text(),
        ];
    }
}
