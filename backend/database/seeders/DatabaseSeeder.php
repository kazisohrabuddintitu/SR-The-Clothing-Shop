<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProductSeeder::class);
        $this->call(ProductSizeSeeder::class);

        User::factory()->create([
            'name' => 'SR Admin',
            'email' => 'admin@sr-clothing.test',
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'SR Customer',
            'email' => 'customer@sr-clothing.test',
        ]);
    }
}
