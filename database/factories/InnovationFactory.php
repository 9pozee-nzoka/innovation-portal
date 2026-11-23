<?php

namespace Database\Factories;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Innovation>
 */
class InnovationFactory extends Factory
{
    protected $model = Innovation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'status' => InnovationStatus::DRAFT,
            'submitted_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InnovationStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InnovationStatus::UNDER_REVIEW,
            'submitted_at' => now()->subDay(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InnovationStatus::APPROVED,
            'submitted_at' => now()->subDays(3),
        ]);
    }
}
