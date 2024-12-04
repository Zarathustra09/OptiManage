<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
           'name' => 'Juan Dela Cruz',
            'phone_number' => '1234567890',
            'email' => 'admin@example.com',
            'password' => Hash::make('Test@123'),
            'role_id' => 1,
        ]);
    }
}
