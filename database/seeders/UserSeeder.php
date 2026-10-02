<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Barangay Captain
        User::create([
            'name' => 'Mark Indayon',
            'email' => 'captain@siemprevivasur.com',
            'password' => Hash::make('password123'),
            'role' => 'captain',
        ]);

        // Barangay Secretary
        User::create([
            'name' => 'Donna Galamgam',
            'email' => 'secretary@siemprevivasur.com',
            'password' => Hash::make('password123'),
            'role' => 'secretary',
        ]);

        // Barangay Staff
        User::create([
            'name' => 'Mariel Ivy Labrador',
            'email' => 'staff@siemprevivasur.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);
    }
}