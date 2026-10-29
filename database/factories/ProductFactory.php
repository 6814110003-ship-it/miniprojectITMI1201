<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'SHFiguarts Monkey D. Luffy',
            'SHFiguarts Roronoa Zoro',
            'SHFiguarts Sanji',
            'SHFiguarts Nami',
            'SHFiguarts Portgas D. Ace',
            'SHFiguarts Sakazuki (Akainu)',
            'SHFiguarts Sabo',
            'SHFiguarts Trafalgar Law',
            'FIGMA Hatsune Miku',
            'FIGMA Kaguya-sama',
            'FIGMA Reimu Hakurei',
            'RE:EDITION Darth Vader',
            'Nendoroid Genshin Impact - Zhongli',
            'Nendoroid Demon Slayer - Tanjiro',
            'Nendoroid One Piece - Luffy',
            'Cardfight!! Vanguard - Blaster Blade',
            'Cardfight!! Vanguard - Dragonic Overlord',
            'Cardfight!! Vanguard - Majesty Lord Blaster',
            'Cardfight!! Vanguard - Phantom Blaster Dragon',
            'Cardfight!! Vanguard - Ren Suzugamori SP',
            'Cardfight!! Vanguard - Blue Storm Supreme Dragon, Glory Maelstrom',
            'Cardfight!! Vanguard - Aichi Sendou Deck Box Set',
            'Cardfight!! Vanguard - Chronojet Dragon',
            'Cardfight!! Vanguard - Gear Chronicle',
            'Cardfight!! Vanguard - Brave Vesperia',
        ];

        $name = $this->faker->randomElement($names) . ' ' . $this->faker->numberBetween(100, 999);
        $status = $this->faker->randomElement(['preorder', 'instock']);

        return [
            'category_id' => Category::query()->inRandomOrder()->first()?->id ?? 1,
            'name' => $name,
            'slug' => Str::slug($name).'-'.uniqid(),
            'code' => 'PF-' . strtoupper(Str::random(8)),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->numberBetween(2900, 18500),
            'deposit' => fn (array $attributes) => $attributes['status'] === 'preorder' ? (int) ($attributes['price'] * 0.3) : 0,
            'status' => $status,
            'size' => $this->faker->randomElement(['150mm', '170mm', '200mm', '250mm', 'Standard']),
            'release_date' => $status === 'preorder' ? now()->addMonths($this->faker->numberBetween(1, 6))->format('Y-m-d') : null,
            'preorder_deadline' => $status === 'preorder' ? now()->addWeeks($this->faker->numberBetween(1, 8))->format('Y-m-d') : null,
            'is_new' => $this->faker->boolean(60),
            'is_featured' => $this->faker->boolean(30),
            'stock' => $status === 'instock' ? $this->faker->numberBetween(5, 50) : 0,
            'is_active' => true,
        ];
    }
}