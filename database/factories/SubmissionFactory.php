<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Submission;

class SubmissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Submission::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->randomNumber(),
            'author_id' => Author::factory(),
            'publisher_id' => Publisher::factory(),
        ];
    }
}
