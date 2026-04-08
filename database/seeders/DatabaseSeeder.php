<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Requires at least one user in `users` (this seeder does not create or modify users).
        $this->call([
            DummyDataSeeder::class,
        ]);
    }
}
