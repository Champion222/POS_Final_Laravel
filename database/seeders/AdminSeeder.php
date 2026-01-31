<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Lorn David',
            'email' => 'lorndavit12@gmail.com',
            'password' => Hash::make('150505'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'NxaYGzz',
            'email' => 'nxaygzz@gmail.com',
            'password' => Hash::make('vathnayt@18'),
            'role' => 'admin',
        ]);
    }
}
