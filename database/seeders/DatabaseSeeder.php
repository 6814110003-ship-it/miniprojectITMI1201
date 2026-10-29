<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $seedPassword = env('SEED_DEFAULT_PASSWORD') ?: Str::password(16);

        Employee::factory()->create([
            'name' => 'Admin PoSFigure',
            'email' => 'admin@posfigure.test',
            'password' => $seedPassword,
            'position' => 'ผู้จัดการร้าน',
            'is_admin' => true,
        ]);

        Employee::factory()->create([
            'name' => 'Staff PoSFigure',
            'email' => 'staff@posfigure.test',
            'password' => $seedPassword,
            'position' => 'พนักงานขาย',
        ]);

        Member::factory()->create([
            'name' => 'Somchai Member',
            'email' => 'member@posfigure.test',
            'password' => $seedPassword,
        ]);

        // Explicit categories
        $catNames = ['Action Figure', 'Nendoroid', 'FIGMA', 'Model Kit', 'Statue', 'Gundam', 'PLAMATEA', 'One:12 Collective', 'Revoltech', 'Trading Card Game', 'Cardfight!! Vanguard'];
        foreach ($catNames as $i => $name) {
            Category::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'sort_order' => $i + 1, 'is_active' => true]
            );
        }

        // Explicit tags including Cardfight!! Vanguard
        $tagNames = ['One Piece', 'Dragon Ball', 'Naruto', 'Demon Slayer', 'Genshin Impact', 'Hatsune Miku', 'Marvel', 'DC', 'Saint Seiya', 'Gundam', 'Fate', 'Hololive', 'Cardfight!! Vanguard', 'Bushiroad', 'Trading Card'];
        foreach ($tagNames as $name) {
            Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
        }

        // Products are NOT seeded — staff add them via the admin web form (requires login).

        $this->command?->warn('Sample account password: '.$seedPassword);
        $this->command?->warn('Set SEED_DEFAULT_PASSWORD in .env to use a fixed password instead.');
    }
}