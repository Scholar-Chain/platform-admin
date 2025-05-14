<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Author;
use App\Models\ConnectorAccessToken;
use App\Models\Publisher;

class ConnectorAccessTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConnectorAccessToken::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'token' => fake()->text(),
            'author_id' => Author::factory(),
            'publisher_id' => Publisher::factory(),
        ];
    }
}
