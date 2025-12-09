<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed units first
        $this->call(UnitSeeder::class);

        // Create admin user
        User::create([
            'name' => 'Fahmie',
            'email' => 'fahmie@gmail.com',
            'password' => Hash::make('elfahmie'),
        ]);
    }
}
