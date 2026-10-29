<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Action Figure',
            'Nendoroid',
            'FIGMA',
            'Model Kit',
            'Statue',
            'Gundam',
            'PLAMATEA',
            'One:12 Collective',
            'Revoltech',
            'Trading Card Game',
            'Cardfight!! Vanguard',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($names),
            'slug' => Str::slug($name ?? 'category'),
            'sort_order' => $this->faker->numberBetween(1, 30),
            'is_active' => true,
        ];
    }
}