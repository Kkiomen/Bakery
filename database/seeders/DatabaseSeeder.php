<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email_verified_at' => now(),
            'email' => 'admin@bakery.local',
            'password' =>  Hash::make('admin'),
            'remember_token' => Str::random(10),
        ]);

        $this->call([
            ProductSeeder::class,
            MaterialSeeder::class,
            RecipeSeeder::class,
        ]);
    }
}
