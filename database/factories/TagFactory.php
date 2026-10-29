<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'One Piece',
            'Dragon Ball',
            'Naruto',
            'Demon Slayer',
            'Genshin Impact',
            'Hatsune Miku',
            'Marvel',
            'DC',
            'Saint Seiya',
            'Gundam',
            'Fate',
            'Hololive',
            'Cardfight!! Vanguard',
            'Bushiroad',
            'Trading Card',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($names),
            'slug' => Str::slug($name ?? 'tag'),
        ];
    }
}